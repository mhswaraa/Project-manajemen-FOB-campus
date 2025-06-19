<?php
// Path: app/Http/Controllers/Investor/ProjectController.php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Tampilkan list proyek aktif, dengan filter (opsional).
     */
    public function index(Request $request)
{
    // Hanya proyek ACTIVE dan sisa quantity > 0
    $query = Project::where('status', 'active')
                    ->where('quantity', '>', 0);

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    $projects = $query->orderBy('deadline','desc')
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
     * Simpan investasi ke tabel investments.
     */
     public function store(Request $request, Project $project)
    {
        $user       = Auth::user();
        $investorId = $user->investor->investor_id;

        // Validasi input (perlu juga validasi qty)
        $data = $request->validate([
            'qty'      => 'required|integer|min:1|max:'.$project->quantity,
            'message'  => 'nullable|string|max:500',
            'receipt'  => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Upload receipt (opsional)
        if ($request->hasFile('receipt')) {
            $data['receipt'] = $request->file('receipt')
                                   ->store('receipts','public');
        }

        // Buat record investasi
        Investment::create([
            'investor_id' => $investorId,
            'project_id'  => $project->id,
            'qty'         => $data['qty'],
            'amount'      => $project->price_per_piece * $data['qty'],
            'message'     => $data['message'] ?? null,
            'receipt'     => $data['receipt'] ?? null,
            'approved'    => false,
        ]);

        // **DECREMENT** stock pada tabel projects
        $project->decrement('quantity', $data['qty']);

        return redirect()
            ->route('investor.projects.index')
            ->with('success','Investasi Anda berhasil disimpan.');
    }

    /**
     * (Admin) Daftar investasi yang masih pending approval.
     */
    public function pendingInvestments()
    {
        $pending = Investment::with(['project', 'investor'])
                     ->where('approved', false)
                     ->latest()
                     ->get();

        return view('admin.projects.pending', compact('pending'));
    }

    /**
     * (Admin) Approve satu investasi.
     */
    public function approveInvestment(Investment $investment)
    {
        $investment->update(['approved' => true]);

        return back()->with('success', 'Investasi ID #'.$investment->id.' berhasil disetujui.');
    }
}
