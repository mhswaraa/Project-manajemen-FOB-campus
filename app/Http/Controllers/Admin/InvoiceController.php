<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Menampilkan detail satu invoice dan form pembayaran.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['tailor.user', 'progressItems.assignment.project', 'processor']);
        
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Memproses pembayaran untuk sebuah invoice.
     */
    public function pay(Request $request, Invoice $invoice)
    {
        // Hanya proses jika status masih pending
        if ($invoice->status !== 'pending') {
            return back()->with('error', 'Invoice ini sudah pernah diproses sebelumnya.');
        }

        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes'   => 'nullable|string',
        ]);

        $receiptPath = $request->file('receipt')->store('invoice_receipts', 'public');

        $invoice->update([
            'status' => 'paid',
            'payment_date' => now(),
            'receipt_path' => $receiptPath,
            'processed_by_user_id' => Auth::id(),
            // notes bisa ditambahkan jika diperlukan
        ]);

        return redirect()->route('admin.invoices.index', ['tab' => 'paid'])
                         ->with('success', 'Pembayaran untuk invoice ' . $invoice->invoice_number . ' berhasil dicatat.');
    }
}
