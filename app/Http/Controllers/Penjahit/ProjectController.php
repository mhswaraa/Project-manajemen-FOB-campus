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
    // Total pcs yang sudah di‑investasikan & approved oleh admin
    $totalInvested = $project->investments()
                             ->where('approved', true)
                             ->sum('qty');

    // Total pcs yang sudah di‑ambil oleh penjahit (semua assignment)
    $alreadyTaken = $project->assignments()
                            ->sum('assigned_qty');

    // Sisa kuota yang benar
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
        // Ambil tailor dari user yang sedang login
        $tailor = Auth::user()->tailor;

        // =======================================================
        // AWAL DARI KODE BARU: Pengecekan Duplikasi
        // =======================================================
        $existingAssignment = ProjectTailor::where('project_id', $project->id)
                                             ->where('tailor_id', $tailor->tailor_id)
                                             ->first();

        if ($existingAssignment) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah mengambil proyek ini. Silakan kerjakan tugas yang ada di halaman "Tugas Saya".');
        }
        // =======================================================
        // AKHIR DARI KODE BARU
        // =======================================================

        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        // Hitung sisa kuota dengan cara yang sama seperti di method create()
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

        // Simpan assignment ke tabel project_tailors
        ProjectTailor::create([
            'project_id'   => $project->id,
            'tailor_id'    => $tailor->tailor_id,
            'assigned_qty' => $data['qty'],
            'started_at'   => now(),
            'status'       => 'in_progress',
        ]);

        return redirect()
            ->route('penjahit.tasks.index')
            ->with('success', "Tugas berhasil diambil ({$data['qty']} pcs).");
    }
}
