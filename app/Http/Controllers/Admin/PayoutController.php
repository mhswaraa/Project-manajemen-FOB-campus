<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\Project;
use App\Models\Payout; // Import model Payout
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Menampilkan daftar pembayaran profit (siap dibayar atau riwayat).
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'unpaid'); // Default tab adalah 'unpaid'

        $payoutsReady = collect();
        $payoutHistory = collect();

        if ($tab === 'unpaid') {
            // --- Logika untuk Tab "Siap Dibayar" ---
            $projects = Project::with([
                'investments' => function($query) {
                    $query->where('approved', true)->where('profit_payout_status', 'unpaid');
                },
                'investments.investor.user',
                'assignments',
                'progress'
            ])->get();

            $completedProjects = $projects->filter(function ($project) {
                $totalAssigned = $project->assignments->sum('assigned_qty');
                $totalCompleted = $project->progress->sum('quantity_done');
                return $totalAssigned > 0 && $totalCompleted >= $totalAssigned;
            });

            $payoutsReady = $completedProjects->flatMap(fn($project) => $project->investments);

        } else { // tab === 'history'
            // --- Logika untuk Tab "Riwayat Pembayaran" ---
            $query = Payout::with(['investment.project', 'investment.investor.user', 'processor'])
                                ->latest('payment_date');
            
            /** @var \Illuminate\Pagination\LengthAwarePaginator $payoutHistory */ // <-- INI ADALAH PERBAIKANNYA
            $payoutHistory = $query->paginate(15)->withQueryString();
        }

        return view('admin.payouts.index', compact('payoutsReady', 'payoutHistory', 'tab'));
    }

    /**
     * Menyimpan data pembayaran profit (payout).
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

        $receiptPath = $request->file('receipt')->store('payout_receipts', 'public');
        $profitAmount = $investment->qty * $investment->project->profit;

        DB::transaction(function () use ($investment, $receiptPath, $profitAmount, $request) {
            // 1. Buat record di tabel payouts
            $investment->payout()->create([
                'profit_amount' => $profitAmount,
                'payment_date' => now(),
                'receipt_path' => $receiptPath,
                'notes' => $request->notes,
                'processed_by_user_id' => Auth::id(),
            ]);

            // 2. Update status pembayaran profit di tabel investments
            $investment->update(['profit_payout_status' => 'paid']);
        });

        // Redirect kembali ke tab "Siap Dibayar"
        return redirect()->route('admin.payouts.index', ['tab' => 'unpaid'])
                         ->with('success', 'Pembayaran profit berhasil dicatat.');
    }
}
