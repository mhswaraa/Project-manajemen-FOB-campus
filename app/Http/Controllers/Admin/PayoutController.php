<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Payout;
use Illuminate\Http\Request;
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
        // 1. Dapatkan tab yang aktif dari request, default ke 'unpaid'
        $tab = $request->input('tab', 'unpaid');

        // 2. Query dasar untuk investasi yang proyeknya sudah selesai dan disetujui
        $baseQuery = Investment::with(['investor.user', 'project', 'payout'])
            ->where('approved', true)
            ->whereHas('project', fn($q) => $q->where('status', 'completed'));

        // 3. Ambil data berdasarkan tab yang aktif
        $payoutsReady = (clone $baseQuery)->where('profit_payout_status', 'unpaid')->get();
        $payoutHistory = (clone $baseQuery)->where('profit_payout_status', 'paid')->latest('updated_at')->get();

        // 4. Kirim semua data yang dibutuhkan ke view, termasuk variabel $tab
        return view('admin.payouts.index', compact('payoutsReady', 'payoutHistory', 'tab'));
    }

    /**
     * Memproses pembayaran untuk satu atau lebih investasi.
     */
    public function process(Request $request)
    {
        $request->validate([
            'investment_ids' => 'required|array|min:1',
            'investment_ids.*' => 'exists:investments,id',
            'receipt' => 'required|image|max:2048', // Bukti transfer
        ]);

        try {
            $receiptPath = $request->file('receipt')->store('payout_receipts', 'public');

            foreach ($request->investment_ids as $investmentId) {
                $investment = Investment::find($investmentId);
                if ($investment && $investment->profit_payout_status === 'unpaid') {
                    // Buat record payout baru
                    Payout::create([
                        'investment_id' => $investment->id,
                        'amount' => $investment->profit,
                        'paid_at' => now(),
                        'receipt_path' => $receiptPath,
                    ]);

                    // Update status investasi menjadi 'paid'
                    $investment->update(['profit_payout_status' => 'paid']);
                }
            }

            return redirect()->route('admin.payouts.index', ['tab' => 'history'])->with('success', 'Pembayaran berhasil diproses.');

        } catch (\Exception $e) {
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
