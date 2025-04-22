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
                    // Ambil record investor
                    $invRecord = Auth::user()->investor;
    
                    // Jika belum ada data diri, redirect ke halaman profil
                    if (! $invRecord) {
                        return redirect()
                            ->route('investor.profile')
                            ->with('warning', 'Silakan lengkapi data diri Anda terlebih dahulu.');
                    }
    
                    $investorId    = $invRecord->investor_id;
                    $totalInvested = Investment::where('investor_id', $investorId)->sum('amount');
                    $projectsCount = Investment::where('investor_id', $investorId)
                                               ->distinct('project_id')
                                               ->count('project_id');
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
