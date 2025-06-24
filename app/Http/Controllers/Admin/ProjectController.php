<?php
// Path: app/Http/Controllers/Admin/ProjectController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Investment; // Pastikan ini di-import
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
    
     public function index()
    {
        // Ambil semua proyek dan eager-load data agregat untuk performa
        $projects = Project::withSum(['investments as invested_qty' => function($q) {
            $q->where('approved', true);
        }], 'qty')
        ->withSum('progress as completed_qty', 'quantity_done')
        ->latest() // Urutkan dari yang terbaru
        ->get();

        // Hitung statistik untuk kartu ringkasan
        $activeProjectsCount = $projects->where('status', 'active')->count();
        $completedProjectsCount = $projects->filter(function($p) {
            return ($p->completed_qty ?? 0) >= $p->quantity;
        })->count();
        $fundingNeededCount = $projects->where('status', 'active')->filter(function($p) {
            return ($p->invested_qty ?? 0) < $p->quantity;
        })->count();

        return view('admin.projects.index', compact(
            'projects',
            'activeProjectsCount',
            'completedProjectsCount',
            'fundingNeededCount'
        ));
    }
    
    // ... method lainnya (store, edit, update, destroy) tidak perlu diubah ...
    // ... Pastikan semua method lain tetap ada ...

    /**
     * Simpan proyek baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price_per_piece'  => 'required|numeric|min:0',
            'material_cost'    => 'required|numeric|min:0', // Validasi baru
            'quantity'         => 'required|integer|min:1',
            'profit'           => 'required|numeric|min:0',
            'convection_profit'=> 'required|numeric|min:0',
            'wage_per_piece'   => 'required|numeric|min:0',
            'deadline'         => 'required|date|after_or_equal:today',
            'status'           => 'required|in:' . Project::STATUS_ACTIVE . ',' . Project::STATUS_INACTIVE,
            'image'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

     public function edit(Project $project)
    {
        $project->load([
            'investments' => function($query) {
                $query->where('approved', true)->with('investor.user');
            },
            'assignments.tailor.user',
            'progress'
        ]);

        $investedQty = $project->investments->sum('qty');
        $fundingPercentage = $project->quantity > 0 ? round(($investedQty / $project->quantity) * 100) : 0;

        $completedQty = $project->progress->sum('quantity_done');
        $productionPercentage = $investedQty > 0 ? round(($completedQty / $investedQty) * 100) : 0;
        
        $totalFunds = $project->investments->sum('amount');
        $potentialInvestorProfit = $project->profit * $project->quantity;
        $potentialConvectionProfit = $project->convection_profit * $project->quantity;
        $totalWageCost = ($project->wage_per_piece ?? 0) * $project->quantity;

        return view('admin.projects.edit', compact(
            'project',
            'investedQty',
            'fundingPercentage',
            'completedQty',
            'productionPercentage',
            'totalFunds',
            'potentialInvestorProfit',
            'potentialConvectionProfit',
            'totalWageCost'
        ));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price_per_piece'  => 'required|numeric|min:0',
            'material_cost'    => 'required|numeric|min:0', // Validasi baru
            'quantity'         => 'required|integer|min:1',
            'profit'           => 'required|numeric|min:0',
            'convection_profit'=> 'required|numeric|min:0',
            'wage_per_piece'   => 'required|numeric|min:0',
            'deadline'         => 'required|date',
            'status'           => 'required|in:' . Project::STATUS_ACTIVE . ',' . Project::STATUS_INACTIVE,
            'image'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dihapus.');
    }
    /**
 * @return \Illuminate\View\View
 */
    public function invested(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query = Investment::with(['investor.user', 'project'])->latest();

        if ($status === 'approved') {
            $query->where('approved', true);
        } else {
            $query->where('approved', false);
        }
        
        $investments = $query->paginate(10)->withQueryString();

        $pendingCount = Investment::where('approved', false)->count();
        $approvedCount = Investment::where('approved', true)->count();
        $totalInvestedAmount = Investment::where('approved', true)->sum('amount');

        return view('admin.projects.invested', compact(
            'investments',
            'pendingCount',
            'approvedCount',
            'totalInvestedAmount',
            'status'
        ));
    }

    public function approveInvestment(Investment $investment)
    {
        $investment->update(['approved' => true]);

        return redirect()
            ->route('admin.projects.invested', ['status' => 'pending'])
            ->with('success', 'Investasi ID #' . $investment->id . ' berhasil disetujui.');
    }

     /**
     * Menampilkan halaman detail komprehensif untuk sebuah proyek.
     */
    public function show(Project $project)
    {
        $project->load([
            'investments' => fn($q) => $q->where('approved', true)->with('investor.user'),
            'assignments.tailor.user',
            'assignments.progress'
        ]);

        $investedQty = $project->investments->sum('qty');
        $fundingPercentage = $project->quantity > 0 ? round(($investedQty / $project->quantity) * 100) : 0;

        $completedQty = $project->assignments->flatMap->progress->sum('quantity_done');
        $productionPercentage = $investedQty > 0 ? round(($completedQty / $investedQty) * 100) : 0;
        
        $totalFunds = $project->investments->sum('amount');
        $potentialInvestorProfit = $project->profit * $investedQty;
        $potentialConvectionProfit = $project->convection_profit * $investedQty;
        $totalWageCost = $project->wage_per_piece * $completedQty;
        $netPotentialProfit = $potentialConvectionProfit - ($project->wage_per_piece * $investedQty);
        
        return view('admin.projects.show', compact(
            'project',
            'investedQty',
            'fundingPercentage',
            'completedQty',
            'productionPercentage',
            'totalFunds',
            'potentialInvestorProfit',
            'potentialConvectionProfit',
            'totalWageCost',
            'netPotentialProfit'
        ));
    }
}