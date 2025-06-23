<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Investment;
use App\Models\Project;
use App\Models\Tailor;
use App\Models\TailorProgress;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman utama laporan.
     */
    public function index(Request $request)
    {
        // 1. Tangani Filter Rentang Tanggal
        // Jika tidak ada filter, default ke bulan ini.
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        // Konversi ke instance Carbon untuk query
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 2. Kalkulasi Data Laporan Keuangan
        $totalFunds = Investment::where('approved', true)
                                ->whereBetween('created_at', [$start, $end])
                                ->sum('amount');
        
        $totalWagesPaid = DB::table('tailor_progress')
                            ->join('project_tailor', 'tailor_progress.assignment_id', '=', 'project_tailor.id')
                            ->join('projects', 'project_tailor.project_id', '=', 'projects.id')
                            ->whereBetween('tailor_progress.date', [$start, $end])
                            ->sum(DB::raw('tailor_progress.quantity_done * projects.wage_per_piece'));

        $totalProfit = DB::table('investments')
                         ->join('projects', 'investments.project_id', '=', 'projects.id')
                         ->where('investments.approved', true)
                         ->whereBetween('investments.created_at', [$start, $end])
                         ->sum(DB::raw('investments.qty * projects.profit'));
                         
        // 3. Kalkulasi Data Laporan Kinerja
        // Proyek paling profitabel dalam rentang waktu
        $topProjects = Project::withSum(['investments as realized_qty' => function($q) use ($start, $end) {
                                $q->where('approved', true)->whereBetween('created_at', [$start, $end]);
                            }], 'qty')
                            ->get()
                            ->map(function ($project) {
                                $project->realized_profit = $project->profit * ($project->realized_qty ?? 0);
                                return $project;
                            })
                            ->where('realized_profit', '>', 0)
                            ->sortByDesc('realized_profit')
                            ->take(5);

        // Penjahit paling produktif dalam rentang waktu
        $topTailors = Tailor::with('user', 'specializations')
                        ->withSum(['progress as total_done' => function($q) use ($start, $end) {
                            $q->whereBetween('date', [$start, $end]);
                        }], 'quantity_done')
                        ->having('total_done', '>', 0)
                        ->orderBy('total_done', 'desc')
                        ->take(5)
                        ->get();

        return view('admin.reports.index', compact(
            'startDate', 'endDate',
            'totalFunds', 'totalWagesPaid', 'totalProfit',
            'topProjects', 'topTailors'
        ));
    }
}