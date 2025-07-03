<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PayoutController extends Controller
{
    /**
     * Menampilkan halaman manajemen pembayaran profit investor dengan sistem tab.
     */
    public function index(Request $request)
{
    $tab = $request->input('tab', 'unpaid');

    // Query untuk tab "Siap Dibayar" (tidak berubah, ini sudah benar)
    $payoutsReady = Investment::with(['investor.user', 'project'])
        ->where('approved', true)
        ->whereHas('project', fn($q) => $q->where('status', 'completed'))
        ->where('profit_payout_status', 'unpaid')
        ->get();

    // ================== PERBAIKAN DI SINI ==================
    // Query untuk Riwayat Pembayaran, sekarang mengambil dari model Payout.
    // Kita juga memuat relasi-relasi yang dibutuhkan oleh view.
    $payoutHistory = Payout::with(['investment.investor.user', 'investment.project'])
        ->latest('paid_at') // Urutkan berdasarkan tanggal pembayaran terbaru
        ->paginate(15); // Gunakan paginasi agar rapi
    // =======================================================

    return view('admin.payouts.index', compact('payoutsReady', 'payoutHistory', 'tab'));
}

    /**
     * ====================================================================
     * METHOD YANG DIPERBAIKI
     * ====================================================================
     * Memproses pembayaran untuk satu investasi.
     */
   public function process(Request $request)
{
    $request->validate([
        'investment_id' => 'required|exists:investments,id',
        'receipt'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    DB::beginTransaction();
    try {
        $investment = Investment::findOrFail($request->investment_id);
        if ($investment->profit_payout_status !== 'unpaid') {
            return back()->with('error', 'Pembayaran untuk investasi ini sudah diproses sebelumnya.');
        }
        $receiptPath = $request->file('receipt')->store('payout_receipts', 'public');
        Payout::create([
            'investment_id' => $investment->id,
            'amount'        => $investment->profit, // Sesuai dengan nama kolom baru
            'paid_at'       => now(),              // Sesuai dengan nama kolom baru
            'receipt_path'  => $receiptPath,
        ]);
        $investment->update(['profit_payout_status' => 'paid']);
        DB::commit();
        return redirect()->route('admin.payouts.index', ['tab' => 'history'])->with('success', 'Pembayaran berhasil diproses.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error processing payout: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan saat memproses payout. Silakan coba lagi.');
    }
}

    /**
     * Menampilkan detail pembayaran.
     */
    public function show(Payout $payout)
    {
        $payout->load('investment.investor.user', 'investment.project');
        return view('admin.payouts.show', compact('payout'));
    }

    /**
     * Mengunduh bukti pembayaran asli yang diunggah.
     */
    public function downloadReceipt(Payout $payout)
    {
        if (!$payout->receipt_path || !Storage::disk('public')->exists($payout->receipt_path)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan.');
        }
        return Storage::disk('public')->download($payout->receipt_path);
    }

    /**
     * Mengunduh rekap pembayaran dalam format PDF.
     */
    public function downloadPdf(Payout $payout)
    {
        $payout->load('investment.investor.user', 'investment.project');
        $pdf = Pdf::loadView('admin.payouts.pdf', compact('payout'));
        
        return $pdf->download('bukti-payout-'.$payout->id.'.pdf');
    }
}
