<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Tampilkan list proyek aktif, dengan filter (opsional).
     */
    public function index(Request $request)
    {
        // Base query: hanya proyek dengan status = 'active'
        $query = Project::where('status', 'active');

        // Filter by category (jika parameter category diisi)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Ambil dengan paginate, terbaru berdasar deadline
        $projects = $query->orderBy('deadline', 'desc')
                          ->paginate(10);

        return view('investor.projects.index', compact('projects'));
    }

    /**
     * Form investasi untuk proyek tertentu.
     */
    public function create(Project $project)
    {
        return view('investor.projects.invest', compact('project'));
    }

    /**
     * Simpan investasi ke tabel pivot investments.
     */
    public function store(Request $request, Project $project)
    {
        $user       = Auth::user();
        $investorId = $user->investor->investor_id;

        // Validasi input
        $data = $request->validate([
            'amount'   => 'required|numeric|min:1|max:'.$project->budget,
            'deadline' => 'required|date|after_or_equal:today',
            'message'  => 'nullable|string|max:500',
            'receipt'  => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Handle upload bukti (opsional)
        if ($request->hasFile('receipt')) {
            $data['receipt'] = $request->file('receipt')->store('receipts', 'public');
        }

        // Buat record investasi
        Investment::create([
            'investor_id' => $investorId,
            'project_id'  => $project->id,
            'amount'      => $data['amount'],
            'deadline'    => $data['deadline'],
            'message'     => $data['message'] ?? null,
            'receipt'     => $data['receipt'] ?? null,
        ]);

        return redirect()
            ->route('investor.projects.index')
            ->with('success', 'Investasi Anda berhasil disimpan.');
    }
}
