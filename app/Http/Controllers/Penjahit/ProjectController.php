<?php
// Path: app/Http/Controllers/Penjahit/ProjectController.php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTailor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Pastikan Request di-import


class ProjectController extends Controller
{
/**
 * Menampilkan daftar proyek yang tersedia untuk penjahit.
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\View\View
 *
 * @property-read int $remaining
 */
    public function index(Request $request)
    {
        // 1. Ambil query builder untuk model Project
        $query = Project::query();

        // 2. Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Eager-load data agregat yang dibutuhkan
        $query->withSum(['investments as invested_qty' => function ($q) {
            $q->where('approved', true);
        }], 'qty')
              ->withSum('assignments as taken_qty', 'assigned_qty');

        // 4. Terapkan pengurutan dasar
        // Default sort by deadline
        $sortBy = $request->input('sort_by', 'deadline');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        // Hanya terapkan order by jika kolom ada di tabel project
        if (in_array($sortBy, ['deadline', 'name'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // 5. Ambil data dari database
        $projects = $query->get()
            // 6. Hitung sisa kuota dan filter proyek yang masih tersedia
            ->map(function ($project) {
                $invested = $project->invested_qty ?? 0;
                $taken = $project->taken_qty ?? 0;
                $project->remaining = max(0, $invested - $taken);
                return $project;
            })
            ->filter(fn ($p) => $p->remaining > 0);
            
        // 7. Jika sort by 'remaining', urutkan collection setelah dihitung
        if ($sortBy === 'remaining') {
             $projects = $sortDirection === 'asc'
                ? $projects->sortBy('remaining')
                : $projects->sortByDesc('remaining');
        }

        // 8. Kirim data ke view, termasuk input request untuk UI
        return view('penjahit.projects.index', [
            'projects' => $projects,
            'search' => $request->search,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * Form Ambil Proyek (assignment) untuk Penjahit.
     * Tampilkan sisa kuota sebelum ambil tugas.
     */
   public function create(Project $project)
    {
        $totalInvested = $project->investments()
                                  ->where('approved', true)
                                  ->sum('qty');

        $alreadyTaken = $project->assignments()
                                ->sum('assigned_qty');

        $remaining = max(0, $totalInvested - $alreadyTaken);

        return view('penjahit.projects.take', compact(
            'project',
            'totalInvested',
            'alreadyTaken',
            'remaining'
        ));
    }

     /**
     * Simpan assignment Penjahit ke proyek.
     */
    public function store(Request $request, Project $project)
    {
        $tailor = Auth::user()->tailor;

        // ====================================================================
        // AWAL PERUBAHAN: Logika pengecekan duplikasi tugas
        // ====================================================================
        $activeAssignment = ProjectTailor::where('project_id', $project->id)
                                           ->where('tailor_id', $tailor->tailor_id)
                                           ->where('status', '!=', 'completed') // Cek apakah ada tugas yang BELUM selesai
                                           ->first();

        // Jika ditemukan tugas yang belum selesai, tolak permintaan.
        if ($activeAssignment) {
            return back()
                ->withInput()
                ->with('error', 'Anda masih memiliki tugas aktif untuk proyek ini. Selesaikan dan tunggu proses QC sebelum mengambil tugas baru.');
        }
        // ====================================================================
        // AKHIR PERUBAHAN
        // ====================================================================

        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        // Hitung sisa kuota yang tersedia
        $totalInvested = $project->investments()->where('approved', true)->sum('qty');
        $alreadyTaken = $project->assignments()->sum('assigned_qty');
        $availableQty = max(0, $totalInvested - $alreadyTaken);

        if ($data['qty'] > $availableQty) {
            return back()
                ->withInput()
                ->withErrors([
                    'qty' => "Jumlah melebihi sisa tersedia ({$availableQty} pcs)."
                ]);
        }

        // Simpan assignment baru ke tabel project_tailors
        ProjectTailor::create([
            'project_id'   => $project->id,
            'tailor_id'    => $tailor->tailor_id,
            'assigned_qty' => $data['qty'],
            'started_at'   => now(),
            'status'       => 'in_progress', // Status awal selalu 'in_progress'
        ]);

        return redirect()
            ->route('penjahit.tasks.index')
            ->with('success', "Tugas baru berhasil diambil ({$data['qty']} pcs).");
    }
}
