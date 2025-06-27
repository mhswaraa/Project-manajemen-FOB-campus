<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Payout;
use App\Models\Project;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Menampilkan halaman daftar pembayaran (payout).
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mengambil parameter 'tab' dari URL, defaultnya 'unpaid'
        $tab = $request->query('tab', 'unpaid');

        // Langkah 1: Dapatkan ID proyek yang sudah selesai menggunakan query builder manual.
        $completedProjectIds = DB::table('projects')
            ->select('projects.id', 'projects.quantity')
            ->leftJoin('project_tailor', 'projects.id', '=', 'project_tailor.project_id')
            ->leftJoin('tailor_progress', 'project_tailor.id', '=', 'tailor_progress.assignment_id')
            ->groupBy('projects.id', 'projects.quantity')
            ->havingRaw('IFNULL(SUM(tailor_progress.quantity_done), 0) >= projects.quantity')
            ->pluck('projects.id');

        
        $payoutsReady = Investment::where('approved', 1)
            ->where('profit_payout_status', 'unpaid')
            ->whereIn('project_id', $completedProjectIds)
            ->with('project.investor.user') // Cukup with('project.investor.user') karena project sudah termasuk
            ->get();

        // Langkah 1.5: Hitung profit untuk setiap investasi yang siap dibayar
        foreach ($payoutsReady as $investment) {
            $project = $investment->project;
            if ($project) {
                // Kalkulasi profit: (Harga * Kuantitas * %Profit Proyek) * %Ekuitas Investor
                $investment->profit_to_be_paid = ($project->price * $project->quantity * ($project->profit / 100)) * ($investment->equity_percentage / 100);
            } else {
                $investment->profit_to_be_paid = 0;
            }
        }

        // Menggunakan paginate() agar bisa menggunakan links() di view.
        $payoutHistory = Payout::with('investment.project', 'investment.investor.user')->latest()->paginate(10)->withQueryString();

        return view('admin.payouts.index', compact('payoutsReady', 'payoutHistory', 'tab'));
    }

    /**
     * Memproses pembayaran payout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $request->validate([
            'investment_id' => 'required|exists:investments,id',
            'receipt' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        try {
            $investment = Investment::findOrFail($request->investment_id);
            $project = $investment->project;

            // Menghitung total profit untuk investor
            $totalProfit = ($project->price * $project->quantity * ($project->profit / 100)) * ($investment->equity_percentage / 100);

            // Simpan file bukti pembayaran
            $receiptPath = $request->file('receipt')->store('receipts', 'public');

            // Buat record payout baru
            $payout = Payout::create([
                'investment_id' => $investment->id,
                'amount' => $totalProfit,
                'status' => 'paid',
                'receipt' => $receiptPath,
                'paid_at' => now(),
            ]);

            // Update status profit payout di investment
            $investment->update(['profit_payout_status' => 'paid']);

            return redirect()->route('admin.payouts.index')->with('success', 'Payout berhasil diproses.');
        } catch (\Exception $e) {
            Log::error('Error processing payout: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses payout.');
        }
    }

    /**
     * Menampilkan detail payout.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\View\View
     */
    public function show(Payout $payout)
    {
        return view('admin.payouts.show', compact('payout'));
    }

    /**
     * Mengunduh bukti pembayaran dalam format PDF.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt(Payout $payout)
    {
        $pdf = Pdf::loadView('admin.payouts.pdf', compact('payout'));
        return $pdf->download('receipt-payout-' . $payout->id . '.pdf');
    }
}
