<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Investment;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $investorId = Auth::user()->investor->investor_id;

        // Total investasi (jumlah uang)
        $totalInvested = Investment::where('investor_id',$investorId)
                                   ->sum('amount');

        // Hitung projek yang diikuti
        $projectsCount = Investment::where('investor_id',$investorId)
                                   ->distinct('project_id')
                                   ->count('project_id');

        // (Opsional) Rata-rata progress: 
        // misal pivot ke project lalu avg dari progress di tabel investering_progress
        // $avgProgress = ...;

        // Ambil 5 proyek terakhir yang diinvestasi
        $recentProjects = Investment::with('project')
                            ->where('investor_id',$investorId)
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard.investor', compact(
            'totalInvested','projectsCount','recentProjects'
        ));
    }
}
