<?php
// Path: app/Http/Controllers/Admin/ProjectController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Investment; // Pastikan ini di-import
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi status


class ProjectController extends Controller
{
    
       public function index()
    {
        // Ambil semua proyek dan eager-load data agregat untuk performa
        $projects = Project::withSum(['investments as invested_qty' => function($q) {
            $q->where('approved', true);
        }], 'qty')
        ->withSum(['progress as production_accepted_qty' => function($q) {
            // ====================================================================
            // AWAL PERUBAHAN: Memberi tahu database untuk menggunakan kolom 'status' dari tabel 'tailor_progress'
            // ====================================================================
            $q->where('tailor_progress.status', 'approved');
            // ====================================================================
            // AKHIR PERUBAHAN
            // ====================================================================
        }], 'accepted_qty')
        ->latest() // Urutkan dari yang terbaru
        ->get();

        // Hitung statistik untuk kartu ringkasan
        $activeProjectsCount = $projects->where('status', 'in_progress')->count();
        // PERUBAHAN: Logika proyek selesai disesuaikan
        $completedProjectsCount = $projects->filter(function($p) {
            // Sebuah proyek dianggap selesai jika jumlah yang diterima QC >= target kuantitas proyek
            return ($p->production_accepted_qty ?? 0) >= $p->quantity;
        })->count();
        $fundingNeededCount = $projects->where('status', 'in_progress')->filter(function($p) {
            return ($p->invested_qty ?? 0) < $p->quantity;
        })->count();

        return view('admin.projects.index', compact(
            'projects',
            'activeProjectsCount',
            'completedProjectsCount',
            'fundingNeededCount'
        ));
    }
    
    /**
     * Simpan proyek baru.
     */
    public function store(Request $request)
    {
        // --- AWAL PERUBAHAN ---
        // Menghapus validasi 'status' karena akan diatur otomatis
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'nominal_proyek'        => 'required|numeric|min:0',
            'price_per_piece'       => 'required|numeric|min:0',
            'material_cost'         => 'required|numeric|min:0',
            'quantity'              => 'required|integer|min:1',
            'profit'                => 'required|numeric|min:0',
            'convection_profit'     => 'required|numeric|min:0',
            'wage_per_piece'        => 'required|numeric|min:0',
            'deadline'              => 'required|date|after_or_equal:today',
            'image'                 => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projects', 'public');
        }
        
        // Mengatur status default untuk setiap proyek baru menjadi 'pending'
        $data['status'] = 'pending';

        Project::create($data);
        // --- AKHIR PERUBAHAN ---

        return redirect()->route('admin.projects.index')->with('success', 'Proyek baru berhasil ditambahkan.');
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
        // --- AWAL PERUBAHAN ---
        // Menambahkan validasi untuk dropdown status baru
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'nominal_proyek'        => 'required|numeric|min:0',
            'price_per_piece'       => 'required|numeric|min:0',
            'material_cost'         => 'required|numeric|min:0',
            'quantity'              => 'required|integer|min:1',
            'profit'                => 'required|numeric|min:0',
            'convection_profit'     => 'required|numeric|min:0',
            'wage_per_piece'        => 'required|numeric|min:0',
            'deadline'              => 'required|date',
            'image'                 => 'nullable|image|max:2048',
            // Validasi untuk status baru
            'status'                => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);
        // --- AKHIR PERUBAHAN ---

        if ($request->hasFile('image')) {
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project->update($data);

        // Redirect ke halaman detail agar admin bisa melihat perubahan
        return redirect()->route('admin.projects.show', $project)->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
{
    // =================== KEMBALIKAN KE 'image' ===================
    if ($project->image) { // DIKEMBALIKAN
        Storage::disk('public')->delete($project->image); // DIKEMBALIKAN
    }
    // =============================================================

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
        
        // FIX: Mengganti withQueryString() dengan appends() untuk kompatibilitas linter
        $investments = $query->paginate(10)->appends($request->query());

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
    // --- Data progress yang sudah ada (tidak berubah) ---
    $project->load([
        'investments' => fn($q) => $q->where('approved', true)->with('investor.user'),
        'assignments.tailor.user',
        'assignments.progress'
    ]);

    $investedQty = $project->investments->sum('qty');
    $fundingPercentage = $project->quantity > 0 ? round(($investedQty / $project->quantity) * 100) : 0;
    $completedQty = $project->assignments->flatMap->progress->sum('quantity_done');
    $productionPercentage = $investedQty > 0 ? round(($completedQty / $investedQty) * 100) : 0;

    
    // =================== PERHITUNGAN KEUANGAN (LOGIKA YANG BENAR) ===================
    
    // 1. Ambil Nominal per Pcs dari Buyer (langsung dari database)
    $nominalPerPcs = $project->nominal_proyek;

    // 2. Hitung Total Nominal dari Buyer secara dinamis
    $totalNominalBuyer = $nominalPerPcs * $project->quantity;

    // 3. Hitung semua estimasi lainnya (logika ini sudah benar)
    $estimasiModalInvestor = $project->price_per_piece * $project->quantity;
    $estimasiBiayaBahan = $project->material_cost * $project->quantity;
    $estimasiUpahJahit = $project->wage_per_piece * $project->quantity;
    $totalBiayaProduksi = $estimasiBiayaBahan + $estimasiUpahJahit;
    $estimasiKeuntunganInvestor = $project->profit * $project->quantity;
    $estimasiProfitKonveksi = $project->convection_profit * $project->quantity;

    // =================== AKHIR PERHITUNGAN ===================

    return view('admin.projects.show', compact(
        'project',
        'investedQty', 'fundingPercentage', 'completedQty', 'productionPercentage',
        
        // --- Variabel Keuangan untuk View ---
        'totalNominalBuyer',
        'nominalPerPcs',
        'estimasiModalInvestor',
        'estimasiBiayaBahan',
        'estimasiUpahJahit',
        'totalBiayaProduksi',
        'estimasiKeuntunganInvestor',
        'estimasiProfitKonveksi'
    ));
}
}