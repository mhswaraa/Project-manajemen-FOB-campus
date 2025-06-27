<?php

namespace App\Http\Controllers\Ceo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\Payroll;
use App\Models\Payout;
use App\Models\Project;
use App\Models\Tailor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ... (method investorCohort tetap sama) ...
    public function investorCohort()
    {
        // ... (kode dari langkah sebelumnya)
        // Langkah 1: Dapatkan bulan akuisisi untuk setiap investor.
        $cohorts = DB::table('users as u')
            ->join('investors as inv', 'u.id', '=', 'inv.user_id')
            ->join('investments as i', 'inv.investor_id', '=', 'i.investor_id')
            ->where('u.role', 'investor')
            ->select('u.id as user_id', DB::raw('MIN(DATE_FORMAT(i.created_at, "%Y-%m-01")) as cohort_date'))
            ->groupBy('u.id');

        // Langkah 2: Dapatkan semua bulan aktivitas unik untuk setiap investor.
        $activities = DB::table('users as u')
            ->join('investors as inv', 'u.id', '=', 'inv.user_id')
            ->join('investments as i', 'inv.investor_id', '=', 'i.investor_id')
            ->where('u.role', 'investor')
            ->select('u.id as user_id', DB::raw('DATE_FORMAT(i.created_at, "%Y-%m-01") as activity_date'))
            ->distinct();

        // Langkah 3: Gabungkan data kohort dan aktivitas.
        $data = DB::table('users as u')
            ->joinSub($cohorts, 'cohorts', 'u.id', '=', 'cohorts.user_id')
            ->joinSub($activities, 'activities', 'u.id', '=', 'activities.user_id')
            ->select('cohorts.cohort_date', DB::raw('TIMESTAMPDIFF(MONTH, cohorts.cohort_date, activities.activity_date) as month_number'), 'u.id as user_id')
            ->get();
            
        // Langkah 4: Olah data menjadi format tabel retensi.
        $cohortData = $data->groupBy('cohort_date');
        $cohortSizes = $cohortData->map->pluck('user_id')->map->unique()->map->count();
        $retentionTable = $cohortData->map(function ($cohortActivities, $cohortDate) use ($cohortSizes) {
            return [
                'cohort_date' => Carbon::parse($cohortDate)->isoFormat('MMMM Y'),
                'cohort_size' => $cohortSizes[$cohortDate] ?? 0,
                'retention_counts' => $cohortActivities->groupBy('month_number')->map->pluck('user_id')->map->unique()->map->count(),
            ];
        })->sortByDesc(fn($item, $key) => $key);

        // Langkah 5 (BARU): Hitung Lifetime Value (LTV) per kohort
        $userTotals = DB::table('users as u')
            ->join('investors as inv', 'u.id', '=', 'inv.user_id')
            ->join('investments as i', 'inv.investor_id', '=', 'i.investor_id')
            ->where('i.approved', true)
            ->select('u.id as user_id', DB::raw('SUM(i.amount) as total_invested'))
            ->groupBy('u.id');

        $cohortLtvData = DB::table('users as u')
            ->joinSub($cohorts, 'cohorts', 'u.id', '=', 'cohorts.user_id')
            ->leftJoinSub($userTotals, 'userTotals', 'u.id', '=', 'userTotals.user_id')
            ->select('cohorts.cohort_date', DB::raw('AVG(userTotals.total_invested) as average_ltv'))
            ->groupBy('cohorts.cohort_date')
            ->orderBy('cohorts.cohort_date', 'desc')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->cohort_date)->isoFormat('MMMM Y') => $item->average_ltv ?? 0]);

        // Langkah 6 (BARU): Hitung KPI Ringkasan
        $overallLtv = $cohortLtvData->avg() ?? 0;
        $month1RetentionRates = $retentionTable->map(function($data){
            if($data['cohort_size'] == 0) return 0;
            $month1Count = $data['retention_counts'][1] ?? 0; // Ambil data retensi bulan ke-1
            return ($month1Count / $data['cohort_size']) * 100;
        });
        $averageRetentionMonth1 = $month1RetentionRates->avg() ?? 0;

        return view('ceo.reports.investor-cohort', [
            'retentionTable' => $retentionTable,
            'ltvData' => $cohortLtvData,
            'maxMonths' => 12,
            'overallLtv' => $overallLtv,
            'averageRetentionMonth1' => $averageRetentionMonth1
        ]);
    }

    /**
     * Menampilkan laporan Papan Peringkat & Efisiensi Produksi.
     */
    public function productionLeaderboard(Request $request)
    {
        $days = $request->input('period', 30);
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $topProductiveTailors = Tailor::with('user', 'specializations')
            ->withSum(['progress as total_done' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }], 'quantity_done')
            ->having('total_done', '>', 0)
            ->orderByDesc('total_done')
            ->take(5)
            ->get();
            
        $completedProjects = Project::whereHas('progress', function($query) {
            $query->select(DB::raw('SUM(quantity_done) as total_completed'))
                  ->groupBy('assignment_id')
                  ->havingRaw('total_completed >= (SELECT assigned_qty FROM project_tailor WHERE id = tailor_progress.assignment_id)');
        })
        ->withMin('investments as start_date', 'created_at')
        ->withMax('progress as end_date', 'date')
        ->get();
        $cycleTimes = $completedProjects->map(function ($project) {
            if ($project->start_date && $project->end_date) {
                return Carbon::parse($project->end_date)->diffInDays(Carbon::parse($project->start_date));
            }
            return null;
        })->filter();
        $averageProductionCycle = $cycleTimes->avg() ?? 0;

        $fastestTailors = DB::table('project_tailor as pt')
            ->join('penjahits', 'pt.tailor_id', '=', 'penjahits.tailor_id')
            ->join('users', 'penjahits.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('AVG(DATEDIFF(pt.completed_at, pt.started_at)) as avg_completion_days'))
            ->whereNotNull('pt.completed_at')->whereNotNull('pt.started_at')
            ->groupBy('users.name')->having('avg_completion_days', '>', 0)
            ->orderBy('avg_completion_days', 'asc')->take(5)->get();

        $specialistData = DB::table('project_tailor as pt')
            ->join('projects as p', 'pt.project_id', '=', 'p.id')
            ->join('penjahits', 'pt.tailor_id', '=', 'penjahits.tailor_id')
            ->join('users', 'penjahits.user_id', '=', 'users.id')
            ->select('users.name', DB::raw("SUBSTRING_INDEX(p.name, ' ', 1) as project_category"), DB::raw('COUNT(p.id) as project_count'))
            ->groupBy('users.name', 'project_category')->orderBy('project_count', 'desc')->get();
            
        $topSpecialists = $specialistData->unique('project_category')->take(5);

        return view('ceo.reports.production-leaderboard', [
            'topProductiveTailors' => $topProductiveTailors,
            'averageProductionCycle' => round($averageProductionCycle),
            'fastestTailors' => $fastestTailors,
            'topSpecialists' => $topSpecialists,
            'currentPeriod' => $days
        ]);
    }

    /**
     * (BARU) Menampilkan halaman peramalan arus kas keluar.
     */
   /**
     * (DIROMBAK) Menampilkan halaman peramalan arus kas (Cash Flow Forecast).
     */
   public function cashFlowForecast(Request $request)
    {
        $investments = Investment::where('approved', true)->with('project')->get();
        $payrolls = Payroll::whereNotNull('payment_date')->get();
        $payouts = Payout::all();

        $monthlyData = [];

        foreach ($investments as $investment) {
            $month = Carbon::parse($investment->created_at)->format('Y-m');
            if (!isset($monthlyData[$month])) $this->initializeMonth($monthlyData, $month);
            $monthlyData[$month]['income'] += $investment->amount;
            $monthlyData[$month]['material_cost'] += $investment->project->material_cost * $investment->qty;
            $monthlyData[$month]['convection_profit'] += $investment->project->convection_profit * $investment->qty;
        }

        foreach ($payrolls as $payroll) {
            $month = Carbon::parse($payroll->payment_date)->format('Y-m');
            if (!isset($monthlyData[$month])) $this->initializeMonth($monthlyData, $month);
            $monthlyData[$month]['wage_cost'] += $payroll->amount;
        }

        foreach ($payouts as $payout) {
            $month = Carbon::parse($payout->payment_date)->format('Y-m');
            if (!isset($monthlyData[$month])) $this->initializeMonth($monthlyData, $month);
            $monthlyData[$month]['investor_payout'] += $payout->amount;
        }

        ksort($monthlyData);

        $forecastData = [];
        foreach ($monthlyData as $month => $data) {
            $totalExpenses = $data['material_cost'] + $data['wage_cost'] + $data['investor_payout'];
            $netCashFlow = $data['income'] - $totalExpenses;
            
            $forecastData[] = [ // Using array_push style
                'month' => Carbon::createFromFormat('Y-m', $month)->isoFormat('MMMM YYYY'),
                'income' => $data['income'],
                'expenses' => $totalExpenses,
                'material_cost' => $data['material_cost'],
                'wage_cost' => $data['wage_cost'],
                'investor_payout' => $data['investor_payout'],
                'net_cash_flow' => $netCashFlow,
                'convection_profit' => $data['convection_profit'],
            ];
        }
        
        $totalIncome = array_sum(array_column($forecastData, 'income'));
        $totalExpenses = array_sum(array_column($forecastData, 'expenses'));
        $totalNetCashFlow = array_sum(array_column($forecastData, 'net_cash_flow'));
        $totalConvectionProfit = array_sum(array_column($forecastData, 'convection_profit'));

        // PENAMBAHAN: Menyiapkan data untuk Chart.js
        $chartLabels = array_column($forecastData, 'month');
        $chartIncomeData = array_column($forecastData, 'income');
        $chartExpensesData = array_column($forecastData, 'expenses');
        $chartNetFlowData = array_column($forecastData, 'net_cash_flow');
        $chartProfitData = array_column($forecastData, 'convection_profit');

        return view('ceo.reports.cash-flow-forecast', compact(
            'forecastData', 
            'totalIncome', 
            'totalExpenses', 
            'totalNetCashFlow', 
            'totalConvectionProfit',
            'chartLabels',
            'chartIncomeData',
            'chartExpensesData',
            'chartNetFlowData',
            'chartProfitData'
        ));
    }

    private function initializeMonth(&$data, $month)
    {
        $data[$month] = [
            'income' => 0, 'material_cost' => 0, 'wage_cost' => 0,
            'investor_payout' => 0, 'convection_profit' => 0,
        ];
    }
}
