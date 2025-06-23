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
     */
    public function create()
    {
        $tailor = Auth::user()->tailor;

        $unbilledProgress = TailorProgress::whereHas('assignment', function ($query) use ($tailor) {
            $query->where('tailor_id', $tailor->tailor_id);
        })
        ->whereNull('invoice_id')
        ->with('assignment.project')
        ->get()
        ->map(function ($progress) {
            $progress->wage = $progress->quantity_done * $progress->assignment->project->wage_per_piece;
            return $progress;
        });

        return view('penjahit.invoices.create', compact('unbilledProgress'));
    }

    /**
     * Menyimpan invoice baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'progress_ids' => 'required|array|min:1',
            'progress_ids.*' => 'exists:tailor_progress,id',
        ]);

        $tailor = Auth::user()->tailor;
        $progressIds = $request->input('progress_ids');

        // PERBAIKAN: Gunakan foreach untuk kalkulasi yang lebih eksplisit
        $progressItems = TailorProgress::with('assignment.project')->find($progressIds);
        $totalAmount = 0;
        foreach ($progressItems as $item) {
            $totalAmount += $item->quantity_done * $item->assignment->project->wage_per_piece;
        }

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
