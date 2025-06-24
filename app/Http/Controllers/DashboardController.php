<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use App\Models\Payout; // Import Payout
use App\Models\ProjectTailor;
use App\Models\TailorProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user->role);

        switch ($role) {
            case 'admin':
                // Logika untuk dasbor admin (tetap sama)
                $projectCount = Project::count();
                $investorCount = User::where('role', 'investor')->count();
                $penjahitCount = User::where('role', 'penjahit')->count();
                $pendingInvestments = Investment::with(['project', 'investor.user'])->where('approved', false)->latest()->take(5)->get();
                $totalApprovedFund = Investment::where('approved', true)->sum('amount');
                $potentialGrossProfit = Project::sum(DB::raw('profit * quantity'));
                $estimatedWageCost = Project::sum(DB::raw('wage_per_piece * quantity'));
                $totalAssignedUnits = ProjectTailor::sum('assigned_qty');
                $totalCompletedUnits = TailorProgress::sum('quantity_done');
                $completionRate = ($totalAssignedUnits > 0) ? round(($totalCompletedUnits / $totalAssignedUnits) * 100) : 0;
                $atRiskProjects = Project::withSum(['assignments as assigned_work'], 'assigned_qty')
                    ->withSum(['progress as completed_work'], 'quantity_done')
                    ->where('status', 'active')
                    ->where('deadline', '>=', now())
                    ->where('deadline', '<=', now()->addDays(7))
                    ->get()
                    ->filter(function ($project) {
                        $assigned = $project->assigned_work ?? 0;
                        if ($assigned == 0) return false;
                        $completed = $project->completed_work ?? 0;
                        $completionPercentage = round(($completed / $assigned) * 100);
                        return $completionPercentage < 75;
                    });
                return view('dashboard.admin', compact('projectCount', 'investorCount', 'penjahitCount', 'totalApprovedFund', 'potentialGrossProfit', 'estimatedWageCost', 'totalAssignedUnits', 'totalCompletedUnits', 'completionRate', 'pendingInvestments', 'atRiskProjects'));

            case 'ceo':
                // Logika untuk Dasbor CEO (Sistem Informasi Eksekutif)
                $totalFundsRaised = Investment::where('approved', true)->sum('amount');
                $totalProfitPaidOut = Payout::sum('profit_amount');
                $totalWagesPaid = DB::table('tailor_progress')->whereNotNull('invoice_id')->join('project_tailor', 'tailor_progress.assignment_id', '=', 'project_tailor.id')->join('projects', 'project_tailor.project_id', '=', 'projects.id')->sum(DB::raw('tailor_progress.quantity_done * projects.wage_per_piece'));
                $netCashFlow = $totalFundsRaised - ($totalWagesPaid + $totalProfitPaidOut);

                $activeProjects = Project::where('status', 'active')->count();

                // PERBAIKAN DI SINI: Query untuk menghitung proyek yang selesai
                $completedProjects = Project::whereRaw('
                    (SELECT SUM(quantity_done) FROM tailor_progress WHERE assignment_id IN (SELECT id FROM project_tailor WHERE project_id = projects.id)) >= 
                    (SELECT SUM(assigned_qty) FROM project_tailor WHERE project_id = projects.id)
                ')->count();

                $totalInvestors = User::where('role', 'investor')->count();

                $monthlyData = [];
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $monthlyData['labels'][] = $month->format('M Y');
                    $monthlyData['funds'][] = Investment::where('approved', true)->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('amount');
                    $monthlyData['payouts'][] = Payout::whereYear('payment_date', $month->year)->whereMonth('payment_date', $month->month)->sum('profit_amount');
                }

                $nearlyFundedProjects = Project::where('status', 'active')
                    ->withSum(['investments as funded_qty' => fn($q) => $q->where('approved', true)], 'qty')
                    ->get()
                    ->filter(fn($p) => $p->quantity > 0 && ($p->funded_qty / $p->quantity) >= 0.8 && ($p->funded_qty / $p->quantity) < 1)
                    ->take(5);

                return view('dashboard.ceo', compact(
                    'totalFundsRaised', 'totalProfitPaidOut', 'netCashFlow',
                    'activeProjects', 'completedProjects', 'totalInvestors',
                    'monthlyData', 'nearlyFundedProjects'
                ));

            case 'investor':
                $investor = $user->investor;
                if (!$investor) {
                    return redirect()->route('investor.profile')->with('warning', 'Silakan lengkapi data diri Anda terlebih dahulu.');
                }

                $approvedInvestments = $investor->investments()->where('approved', true)->with('project')->get();
                $totalInvested = $approvedInvestments->sum('amount');
                $activeProjectsCount = $approvedInvestments->where('project.status', 'active')->pluck('project_id')->unique()->count();
                $estimatedProfit = $approvedInvestments->sum(function($investment) {
                    if ($investment->project) {
                        return $investment->qty * $investment->project->profit;
                    }
                    return 0;
                });
        
                $portfolioAllocation = $approvedInvestments->groupBy('project.name')->map(fn($group) => $group->sum('amount'));
                $chartLabels = $portfolioAllocation->keys();
                $chartData = $portfolioAllocation->values();
                
                $recentActivities = $investor->investments()->with('project')->latest()->take(5)->get();

                return view('dashboard.investor', compact(
                    'totalInvested',
                    'activeProjectsCount',
                    'estimatedProfit',
                    'chartLabels',
                    'chartData',
                    'recentActivities'
                ));

            case 'penjahit':
                if (!$user->tailor) {
                    return redirect()->route('penjahit.profile.index')->with('warning', 'Harap lengkapi profil penjahit Anda terlebih dahulu untuk melanjutkan.');
                }
                $assignments = $user->tailor->assignments()->with('project', 'progress')->get();
                return view('dashboard.penjahit', compact('assignments'));

            default:
                abort(403, 'Unauthorized');
        }
    }
}
