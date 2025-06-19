<?php
// Path: app/Http/Controllers/Penjahit/ProjectController.php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTailor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
    /**
     * Daftar Proyek untuk Penjahit.
     * - status = active
     * - hanya proyek yang sudah ada investasi approved (invested_qty > 0)
     * - hanya proyek dengan sisa kuota > 0
     * - eager‑load invested_qty (sum(qty) dari investasi approved)
     */
     public function index()
    {
        $projects = Project::query()
            // Eager-load sum of approved investments
            ->withSum(['investments as invested_qty' => function($q) {
                $q->where('approved', true);
            }], 'qty')
            // Eager-load sum of assignments (taken by penjahit)
            ->withSum('assignments as taken_qty', 'assigned_qty')
            ->orderBy('deadline', 'asc')
            ->get()
            // Hitung remaining dan filter
            ->map(function($project) {
                $invested = $project->invested_qty ?? 0;
                $taken    = $project->taken_qty    ?? 0;
                $remaining = max(0, $invested - $taken);

                $project->invested = $invested;
                $project->taken    = $taken;
                $project->remaining= $remaining;

                return $project;
            })
            ->filter(fn($p) => $p->remaining > 0)
            ->values();

        return view('penjahit.projects.index', compact('projects'));
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
        // Ambil tailor id dari user auth
        $tailor = Auth::user()->tailor;

        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        // Hitung sisa kuota
        $takenAssignments = $project->assignments_sum_assigned_qty ?? 0;
        $availableQty = $project->quantity - $takenAssignments;

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
