<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Tailor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = strtolower(Auth::user()->role);

        switch ($role) {
            case 'admin':
                // Statistik untuk cards
                $adminCount    = User::where('role', 'admin')->count();
                $investorCount = User::where('role', 'investor')->count();
                $ownerCount    = User::where('role', 'ceo')->count();
                $penjahitCount = User::where('role', 'penjahit')->count();
                $projectCount  = Project::count();

                // Data tabel
                $projects  = Project::all();
                $investors = Investor::all();
                $penjahits = Tailor::all();

                return view('dashboard.admin', compact(
                    'adminCount',
                    'investorCount',
                    'ownerCount',
                    'penjahitCount',
                    'projectCount',
                    'projects',
                    'investors',
                    'penjahits'
                ));

            case 'ceo':
                return view('dashboard.ceo');

            case 'investor':
                // Ambil data investor
                $invRecord     = Auth::user()->investor;
                $investorId    = $invRecord->investor_id;

                // Total investasi
                $totalInvested = Investment::where('investor_id', $investorId)
                                          ->sum('amount');

                // Jumlah proyek berbeda yang diikuti
                $projectsCount = Investment::where('investor_id', $investorId)
                                          ->distinct('project_id')
                                          ->count('project_id');

                // 5 investasi terbaru
                $recentProjects = Investment::with('project')
                                          ->where('investor_id', $investorId)
                                          ->latest()
                                          ->take(5)
                                          ->get();

                return view('dashboard.investor', compact(
                    'invRecord',
                    'totalInvested',
                    'projectsCount',
                    'recentProjects'
                ));

            case 'penjahit':
                $user = Auth::user()->tailor;
                return view('dashboard.penjahit', compact('user'));

            default:
                abort(403, 'Unauthorized');
        }
    }
}
