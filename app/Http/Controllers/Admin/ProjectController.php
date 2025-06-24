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
            'quantity'         => 'required|integer|min:1',
            'profit'           => 'required|numeric|min:0',
            'convection_profit'=> 'required|numeric|min:0', // <-- Tambahkan validasi
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
        
        // Kalkulasi data finansial
        $totalFunds = $project->investments->sum('amount');
        $potentialInvestorProfit = $project->profit * $project->quantity;
        $potentialConvectionProfit = $project->convection_profit * $project->quantity; // <-- KALKULASI BARU
        $totalWageCost = ($project->wage_per_piece ?? 0) * $project->quantity;

        return view('admin.projects.edit', compact(
            'project',
            'investedQty',
            'fundingPercentage',
            'completedQty',
            'productionPercentage',
            'totalFunds',
            'potentialInvestorProfit',
            'potentialConvectionProfit', // <-- Kirim data baru ke view
            'totalWageCost'
        ));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price_per_piece'  => 'required|numeric|min:0',
            'quantity'         => 'required|integer|min:1',
            'profit'           => 'required|numeric|min:0',
            'convection_profit'=> 'required|numeric|min:0', // <-- Tambahkan validasi
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
        // 1. Ambil status tab dari query URL, defaultnya 'pending'
        $status = $request->query('status', 'pending');

        // 2. Buat query dasar dengan relasi yang dibutuhkan
        $query = Investment::with(['investor.user', 'project'])->latest();

        // 3. Terapkan filter berdasarkan tab yang aktif
        if ($status === 'approved') {
            $query->where('approved', true);
        } else {
            // Selain itu, tampilkan yang 'pending' (approved = false)
            $query->where('approved', false);
        }
        
         /** @var \Illuminate\Pagination\LengthAwarePaginator $investments */
    $investments = $query->paginate(10)->withQueryString();

        // 5. Hitung statistik untuk kartu ringkasan
        $pendingCount = Investment::where('approved', false)->count();
        $approvedCount = Investment::where('approved', true)->count();
        $totalInvestedAmount = Investment::where('approved', true)->sum('amount');

        // 6. Kirim semua data ke view
        return view('admin.projects.invested', compact(
            'investments',
            'pendingCount',
            'approvedCount',
            'totalInvestedAmount',
            'status' // Kirim status untuk menandai tab aktif
        ));
    }

    public function approveInvestment(Investment $investment)
    {
        $investment->update(['approved' => true]);

        // Redirect kembali ke tab pending untuk melanjutkan pekerjaan
        return redirect()
            ->route('admin.projects.invested', ['status' => 'pending'])
            ->with('success', 'Investasi ID #' . $investment->id . ' berhasil disetujui.');
    }
}