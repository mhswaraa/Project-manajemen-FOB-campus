<?php
// Path: app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Tailor;
use App\Models\User;
use App\Models\ProjectTailor;
use App\Models\TailorProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request) // Tambahkan Request
    {
        $user = Auth::user();
        $role = strtolower($user->role);

        switch ($role) {
            case 'admin':
                // --- Statistik Entitas Dasar ---
                $projectCount = Project::count();
                $investorCount = User::where('role', 'investor')->count();
                $penjahitCount = User::where('role', 'penjahit')->count();

                // --- IDE 1: Ringkasan Keuangan ---
                $totalApprovedFund = Investment::where('approved', true)->sum('amount');
                $potentialGrossProfit = Project::sum(DB::raw('profit * quantity'));
                // Pastikan kolom 'wage_per_piece' sudah ada di tabel projects
                $estimatedWageCost = Project::sum(DB::raw('wage_per_piece * quantity'));

                // --- IDE 2: Status Operasional ---
                $totalAssignedUnits = ProjectTailor::sum('assigned_qty');
                $totalCompletedUnits = TailorProgress::sum('quantity_done');
                $completionRate = ($totalAssignedUnits > 0) ? round(($totalCompletedUnits / $totalAssignedUnits) * 100) : 0;
                
                // --- Daftar Aksi Utama ---
                $pendingInvestments = Investment::with(['project', 'investor.user'])
                                      ->where('approved', false)
                                      ->latest()
                                      ->take(5)
                                      ->get();
                
                // --- IDE 3: Proyek Berisiko (Deadline < 7 hari & progress < 75%) ---
                $atRiskProjects = Project::withSum('assignments as assigned_work', 'assigned_qty')
                    ->withSum('progress as completed_work', 'quantity_done')
                    ->where('status', 'active') // Hanya proyek aktif
                    ->where('deadline', '>=', now())
                    ->where('deadline', '<=', now()->addDays(7))
                    ->get()
                    ->filter(function ($project) {
                        $assigned = $project->assigned_work ?? 0;
                        if ($assigned == 0) return false;
                        $completed = $project->completed_work ?? 0;
                        $completionPercentage = round(($completed / $assigned) * 100);
                        return $completionPercentage < 75; // Ambil jika progres kurang dari 75%
                    });

                return view('dashboard.admin', compact(
                    'projectCount', 'investorCount', 'penjahitCount',
                    'totalApprovedFund', 'potentialGrossProfit', 'estimatedWageCost',
                    'totalAssignedUnits', 'totalCompletedUnits', 'completionRate',
                    'pendingInvestments', 'atRiskProjects'
                ));

            // ... (case lain tetap sama) ...
            case 'ceo':
                return view('dashboard.ceo');

            case 'investor':
                $invRecord = $user->investor;
                if (!$invRecord) {
                    return redirect()->route('investor.profile')->with('warning', 'Silakan lengkapi data diri Anda terlebih dahulu.');
                }
                $investorId = $invRecord->investor_id;
                $totalInvested = Investment::where('investor_id', $investorId)->sum('amount');
                $projectsCount = Investment::where('investor_id', $investorId)->distinct('project_id')->count('project_id');
                $recentProjects = Investment::with('project')->where('investor_id', $investorId)->latest()->take(5)->get();
                return view('dashboard.investor', compact('invRecord', 'totalInvested', 'projectsCount', 'recentProjects'));

            case 'penjahit':
                if (!$user->tailor) {
                    return redirect()->route('penjahit.profile.index')->with('warning', 'Harap lengkapi profil penjahit Anda terlebih dahulu untuk melanjutkan.');
                }
                $assignments = $user->tailor->assignments()->with('project', 'progress')->get();
                return view('penjahit.dashboard', compact('assignments'));

            default:
                abort(403, 'Unauthorized');
        }
    }
}
