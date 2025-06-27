<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // <-- IMPORT PUSTAKA PDF
use Illuminate\Support\Str; // <-- IMPORT CLASS Str UNTUK MEMBUAT SLUG
use Illuminate\Support\Facades\Storage; // <-- IMPORT FACADE STORAGE

class InvoiceController extends Controller
{
    /**
     * Menampilkan daftar semua invoice.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');

        $query = Invoice::with('tailor.user');

        if ($tab === 'paid') {
            $query->where('status', 'paid');
        } else {
            $query->where('status', 'pending');
        }

        $invoices = $query->latest('issue_date')->paginate(15);

        return view('admin.invoices.index', compact('invoices', 'tab'));
    }

     public function show(Invoice $invoice)
    {
        $invoice->load('tailor.user', 'progressItems.assignment.project', 'processor');
        return view('admin.invoices.show', compact('invoice'));
    }

     public function pay(Request $request, Invoice $invoice)
    {
        $request->validate([
            'receipt' => 'required|image|max:2048',
        ]);

        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        $invoice->update([
            'status' => 'paid',
            'payment_date' => now(),
            'processed_by' => auth()->id(),
            'receipt_path' => $receiptPath,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice telah berhasil ditandai lunas.');
    }


    /**
     * Membuat dan mengunduh faktur dalam format PDF.
     */
    public function downloadPDF(Invoice $invoice)
    {
        // Load data yang dibutuhkan
        $invoice->load('tailor.user', 'progressItems.assignment.project');

        $data = ['invoice' => $invoice];

        // == PENAMBAHAN: LOGIKA UNTUK GAMBAR BUKTI TRANSFER ==
        // Cek jika invoice sudah lunas dan ada path bukti transfer
        if ($invoice->status == 'paid' && $invoice->receipt_path) {
            // Cek apakah file benar-benar ada di storage
            if (Storage::disk('public')->exists($invoice->receipt_path)) {
                // Dapatkan path absolut dari file untuk disematkan ke PDF
                $data['receiptImagePath'] = storage_path('app/public/' . $invoice->receipt_path);
            }
        }

        // Render view 'pdf' dengan data invoice (dan path gambar jika ada)
        $pdf = PDF::loadView('admin.invoices.pdf', $data);

        // Membuat nama file baru
        $tailorName = Str::slug($invoice->tailor->user->name, '-');
        $fileName = $tailorName . '-INV-' . $invoice->invoice_number . '.pdf';

        // Memulai download
        return $pdf->download($fileName);
    }
}
