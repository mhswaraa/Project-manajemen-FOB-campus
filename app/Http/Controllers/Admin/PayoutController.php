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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PayoutController extends Controller
{
    /**
     * Menampilkan halaman daftar pembayaran (payout).
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'unpaid');
        
        $payoutsReady = collect();
        $payoutHistory = collect();

        if ($tab === 'unpaid') {
            // ====================================================================
            // AWAL PERUBAHAN: Logika disederhanakan untuk keandalan dan akurasi
            // ====================================================================

            // Langkah 1: Dapatkan ID semua proyek yang sudah selesai berdasarkan hasil QC.
            $completedProjectIds = Project::query()
                ->whereHas('progress', function ($query) {
                    // Pastikan ada progress yang sudah di-approve
                    $query->where('tailor_progress.status', 'approved');
                })
                ->withSum(['progress as total_accepted' => function ($query) {
                    // Jumlahkan hanya yang sudah di-approve
                    $query->where('tailor_progress.status', 'approved');
                }], 'accepted_qty')
                ->get()
                ->filter(function ($project) {
                    // Proyek dianggap selesai jika total yang diterima QC >= target kuantitas
                    return ($project->total_accepted ?? 0) >= $project->quantity;
                })
                ->pluck('id');

            // Langkah 2: Dapatkan semua investasi yang belum dibayar dari proyek-proyek yang sudah selesai.
            if ($completedProjectIds->isNotEmpty()) {
                $payoutsReady = Investment::where('approved', true)
                    ->where('profit_payout_status', false) // Menggunakan boolean
                    ->whereIn('project_id', $completedProjectIds)
                    ->with(['investor.user', 'project'])
                    ->get();
            }
            
            // Hitung profit untuk setiap investasi yang siap dibayar
            foreach ($payoutsReady as $investment) {
                $project = $investment->project;
                if ($project) {
                    // Logika profit konsisten: kuantitas slot * profit per piece
                    $investment->profit_to_be_paid = $investment->qty * $project->profit;
                } else {
                    $investment->profit_to_be_paid = 0;
                }
            }
            // ====================================================================
            // AKHIR PERUBAHAN
            // ====================================================================
        } else {
            $payoutHistory = Payout::with(['investment.project', 'investment.investor.user', 'processor'])
                ->latest('payment_date')
                ->paginate(10);
        }

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
            'receipt' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        try {
            $investment = Investment::findOrFail($request->investment_id);
            $project = $investment->project;

            // Perhitungan profit yang konsisten
            $totalProfit = $investment->qty * $project->profit;

            // Simpan file bukti pembayaran
            $receiptPath = $request->file('receipt')->store('payout_receipts', 'public');

            // Buat record payout baru
            Payout::create([
                'investment_id' => $investment->id,
                'amount' => $totalProfit,
                'payment_date' => now(),
                'receipt_path' => $receiptPath,
                'processed_by' => Auth::id(),
            ]);

            // Update status profit payout di investment menjadi true
            $investment->update(['profit_payout_status' => true]);

            return redirect()->route('admin.payouts.index', ['tab' => 'history'])->with('success', 'Payout berhasil diproses.');
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
        $payout->load(['investment.project', 'investment.investor.user', 'processor']);
        return view('admin.payouts.show', compact('payout'));
    }

    /**
     * Mengunduh bukti pembayaran.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt(Payout $payout)
    {
        if (!Storage::disk('public')->exists($payout->receipt_path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }
        return Storage::disk('public')->download($payout->receipt_path);
    }
}
