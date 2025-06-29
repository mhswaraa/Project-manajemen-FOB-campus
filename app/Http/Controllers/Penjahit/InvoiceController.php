<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TailorProgress;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Menampilkan halaman untuk membuat invoice baru.
     * Logika diperbarui untuk hanya menampilkan pekerjaan yang sudah disetujui QC.
     */
    public function create()
    {
        $tailor = Auth::user()->tailor;

        // ====================================================================
        // AWAL PERUBAHAN
        // ====================================================================
        $unbilledProgress = TailorProgress::whereHas('assignment', function ($query) use ($tailor) {
            $query->where('tailor_id', $tailor->tailor_id);
        })
        ->whereNull('invoice_id') // Belum ditagih
        ->where('status', 'approved') // HARUS sudah disetujui QC
        ->where('accepted_qty', '>', 0) // HARUS ada item yang diterima
        ->with('assignment.project')
        ->get()
        ->map(function ($progress) {
            // Hitung upah berdasarkan jumlah yang DITERIMA (accepted_qty)
            $progress->wage = $progress->accepted_qty * $progress->assignment->project->wage_per_piece;
            return $progress;
        });
        // ====================================================================
        // AKHIR PERUBAHAN
        // ====================================================================

        return view('penjahit.invoices.create', compact('unbilledProgress'));
    }

    /**
     * Menyimpan invoice baru ke database.
     * Logika diperbarui untuk menghitung total berdasarkan accepted_qty.
     */
    public function store(Request $request)
    {
        $request->validate([
            'progress_ids' => 'required|array|min:1',
            // Pastikan progress_id yang dikirim ada dan milik penjahit ini
            'progress_ids.*' => [
                'exists:tailor_progress,id',
                function ($attribute, $value, $fail) {
                    $progress = TailorProgress::find($value);
                    if ($progress->status !== 'approved' || !is_null($progress->invoice_id)) {
                        $fail('Item yang dipilih tidak valid atau sudah ditagih.');
                    }
                }
            ],
        ]);

        $tailor = Auth::user()->tailor;
        $progressIds = $request->input('progress_ids');

        // ====================================================================
        // AWAL PERUBAHAN
        // ====================================================================
        $progressItems = TailorProgress::with('assignment.project')->find($progressIds);
        
        $totalAmount = 0;
        foreach ($progressItems as $item) {
            // Hitung total HANYA berdasarkan jumlah yang diterima QC
            $totalAmount += $item->accepted_qty * $item->assignment->project->wage_per_piece;
        }
        // ====================================================================
        // AKHIR PERUBAHAN
        // ====================================================================

        if ($totalAmount <= 0) {
            return back()->with('error', 'Gagal membuat invoice. Total tagihan tidak boleh nol.');
        }

        // Buat invoice baru
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'tailor_id' => $tailor->tailor_id,
            'issue_date' => now(),
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Tandai semua progress item dengan invoice_id yang baru
        TailorProgress::whereIn('id', $progressIds)->update(['invoice_id' => $invoice->id]);

        return redirect()->route('penjahit.invoices.index')
                         ->with('success', 'Invoice ' . $invoice->invoice_number . ' berhasil diterbitkan.');
    }

    /**
     * Menampilkan riwayat invoice penjahit.
     */
    public function index()
    {
        $tailor = Auth::user()->tailor;
        $invoices = $tailor->invoices()
                           ->latest('issue_date')
                           ->paginate(10);
        
        return view('penjahit.invoices.index', compact('invoices'));
    }
}