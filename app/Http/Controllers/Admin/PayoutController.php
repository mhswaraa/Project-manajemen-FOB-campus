<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PayoutController extends Controller
{
    /**
     * Menampilkan daftar pembayaran profit (siap dibayar atau riwayat).
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'unpaid');

        $payoutsReady = collect();
        $payoutHistory = collect();

        if ($tab === 'unpaid') {
            $payoutsReady = Investment::where('approved', true)
                ->where('profit_payout_status', 'unpaid')
                ->whereHas('project', function ($query) {
                    $query->whereRaw('(SELECT SUM(quantity_done) FROM tailor_progress tp JOIN project_tailor pt ON tp.project_tailor_id = pt.id WHERE pt.project_id = projects.id) >= projects.quantity');
                })
                ->with('investor.user', 'project')
                ->get();

        } else {
            $query = Payout::with(['investment.project', 'investment.investor.user', 'processor'])
                           ->latest('payment_date');
            
            $payoutHistory = $query->paginate(15)->withQueryString();
        }

        return view('admin.payouts.index', compact('payoutsReady', 'payoutHistory', 'tab'));
    }

    /**
     * Menyimpan data pembayaran profit baru.
     */
    public function store(Request $request, Investment $investment)
    {
        if ($investment->profit_payout_status !== 'unpaid') {
            return back()->with('error', 'Pembayaran profit untuk investasi ini sudah pernah diproses.');
        }

        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);
        
        // FIX: Menghitung total pembayaran (Modal Awal + Profit)
        $profitAmount = $investment->qty * $investment->project->profit;
        $totalPayoutAmount = $investment->amount + $profitAmount; // Ini adalah jumlah total yang dibayarkan
        
        $receiptPath = $request->file('receipt')->store('payout_receipts', 'public');

        DB::transaction(function () use ($investment, $receiptPath, $totalPayoutAmount, $request) {
            Payout::create([
                'investment_id' => $investment->id,
                'amount' => $totalPayoutAmount, // <-- Menggunakan nilai total pembayaran
                'payment_date' => now(),
                'receipt_path' => $receiptPath,
                'notes' => $request->notes,
                'processed_by' => Auth::id(),
            ]);

            $investment->update(['profit_payout_status' => 'paid']);
        });

        return redirect()->route('admin.payouts.index', ['tab' => 'unpaid'])
                         ->with('success', 'Pembayaran profit berhasil dicatat.');
    }

    /**
     * Menampilkan halaman detail untuk sebuah payout.
     */
    public function show(Payout $payout)
    {
        $payout->load('investment.investor.user', 'investment.project', 'processor');
        return view('admin.payouts.show', compact('payout'));
    }
    
    /**
     * Membuat dan menyediakan file PDF untuk diunduh.
     */
    public function downloadPDF(Payout $payout)
    {
        $payout->load('investment.investor.user', 'investment.project', 'processor');
        
        $data = ['payout' => $payout];

        if ($payout->receipt_path && Storage::disk('public')->exists($payout->receipt_path)) {
            $data['receiptImagePath'] = storage_path('app/public/' . $payout->receipt_path);
        }
        
        $pdf = PDF::loadView('admin.payouts.pdf', $data);

        $investorName = Str::slug($payout->investment->investor->user->name, '-');
        $fileName = 'bukti-bayar-profit-' . $investorName . '-ref' . $payout->id . '.pdf';

        return $pdf->download($fileName);
    }
}
