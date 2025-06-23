<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * PERBAIKAN: Mengubah nama method dari __invoke menjadi index
     * untuk menyesuaikan dengan definisi di routes/web.php
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $investor = $user->investor;

        if (!$investor) {
            return redirect()->route('investor.profile')->with('warning', 'Silakan lengkapi data diri Anda terlebih dahulu untuk melanjutkan.');
        }

        // 1. Ambil semua investasi yang disetujui untuk kalkulasi
        $approvedInvestments = $investor->investments()->where('approved', true)->with('project')->get();

        // 2. Data untuk Kartu Statistik
        $totalInvested = $approvedInvestments->sum('amount');
        $activeProjectsCount = $approvedInvestments->where('project.status', 'active')->pluck('project_id')->unique()->count();
        
        $estimatedProfit = $approvedInvestments->sum(function($investment) {
            return $investment->qty * $investment->project->profit;
        });

        // 3. Data untuk Pie Chart Alokasi Dana
        $portfolioAllocation = $approvedInvestments->groupBy('project.name')
            ->map(function ($group) {
                return $group->sum('amount');
            });
        
        $chartLabels = $portfolioAllocation->keys();
        $chartData = $portfolioAllocation->values();
        
        // 4. Data untuk Feed Aktivitas Terbaru
        $recentActivities = $investor->investments()
                                ->with('project')
                                ->latest()
                                ->take(5)
                                ->get();

        return view('dashboard.investor', compact(
            'totalInvested',
            'activeProjectsCount',
            'estimatedProfit',
            'chartLabels',
            'chartData',
            'recentActivities'
        ));
    }
}
