<?php
// Path: app/Http/Controllers/Investor/InvestmentController.php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Investment;
use App\Models\TailorProgress;

class InvestmentController extends Controller
{
    public function __construct()
    {
        // Hanya investor yang boleh mengakses semua method di sini
        $this->middleware(['auth','role:investor']);
    }

    /**
     * List semua investasi milik investor saat ini.
     */
    public function index()
{
    $investorId = Auth::user()->investor->investor_id;

    $investments = Investment::with([
            'project', 
            'project.progress'   // <-- eager‐load hasManyThrough TailorProgress
        ])
        ->where('investor_id', $investorId)
        ->latest()
        ->get();

    return view('investor.investments.index', compact('investments'));
}

    /**
     * Tampilkan form edit investasi.
     */
    public function edit(Investment $investment)
    {
        // Pastikan investor hanya bisa edit investasinya sendiri
        if (Auth::user()->investor->investor_id !== $investment->investor_id) {
            abort(403, 'Anda tidak diizinkan mengedit investasi ini.');
        }

        return view('investor.investments.edit', compact('investment'));
    }

    /**
     * Simpan pembaruan investasi.
     */
    public function update(Request $request, Investment $investment)
    {
        if (Auth::user()->investor->investor_id !== $investment->investor_id) {
            abort(403, 'Anda tidak diizinkan mengubah investasi ini.');
        }

        $data = $request->validate([
            'qty'     => 'required|integer|min:1',
            'amount'  => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
            'receipt' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Handle upload ulang bukti
        if ($request->hasFile('receipt')) {
            if ($investment->receipt) {
                Storage::disk('public')->delete($investment->receipt);
            }
            $data['receipt'] = $request->file('receipt')->store('receipts','public');
        }

        $investment->update($data);

        return redirect()
            ->route('investor.investments.index')
            ->with('success','Investasi #'.$investment->id.' berhasil diperbarui.');
    }

    /**
     * Hapus/batalkan investasi.
     */
    public function destroy(Investment $investment)
    {
        if (Auth::user()->investor->investor_id !== $investment->investor_id) {
            abort(403, 'Anda tidak diizinkan membatalkan investasi ini.');
        }

        if ($investment->receipt) {
            Storage::disk('public')->delete($investment->receipt);
        }
        $investment->delete();

        return redirect()
            ->route('investor.investments.index')
            ->with('success','Investasi #'.$investment->id.' berhasil dibatalkan.');
    }

    /**
     * Tampilkan detail satu investasi.
     */
    public function show(Investment $investment)
{
    // Eager‑load relasi
    $investment->load([
        'project', 
        'project.progress'   // pakai relasi baru
    ]);

    // Hitung total progress produksi dari hasManyThrough
    $doneQty = $investment->project
                  ->progress()
                  ->sum('quantity_done');

    $qty     = $investment->qty;
    $pctDone = $qty ? round($doneQty / $qty * 100) : 0;

    return view('investor.investments.show', compact(
        'investment', 'doneQty', 'pctDone'
    ));
}

}
