<?php
// Path: app/Http/Controllers/Investor/InvestmentController.php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;    // ← tambahkan ini
use App\Models\Investment;

class InvestmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:investor']);
    }

    public function index()
    {
        $investorId = Auth::user()->investor->investor_id;

        $investments = Investment::with([
                'project',
                'project.productionProgress'
            ])
            ->where('investor_id', $investorId)
            ->latest('created_at')
            ->get();

        return view('investor.investments.index', compact('investments'));
    }

    public function edit(Investment $investment)
    {
        if (Auth::user()->investor->investor_id !== $investment->investor_id) {
            abort(403, 'Anda tidak diizinkan mengedit investasi ini.');
        }

        return view('investor.investments.edit', compact('investment'));
    }

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
}
