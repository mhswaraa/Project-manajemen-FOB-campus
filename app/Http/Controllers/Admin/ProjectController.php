<?php
// Path: app/Http/Controllers/Admin/ProjectController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Investment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
    /**
     * Tampilkan daftar proyek.
     */
    public function index()
    {
        // Hanya proyek dengan sisa qty > 0, atau semua tergantung kebutuhan
        $projects = Project::orderBy('id', 'desc')->get();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Simpan proyek baru.
     */
    public function store(Request $request)
    {
        // Validasi input sesuai kolom baru
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price_per_piece'  => 'required|numeric|min:0',
            'quantity'         => 'required|integer|min:1',
            'profit'           => 'required|numeric|min:0',
            'deadline'         => 'required|date|after_or_equal:today',
            'status'           => 'required|in:' . Project::STATUS_ACTIVE . ',' . Project::STATUS_INACTIVE,
            // jika butuh image, tambahkan validasi di sini
            // 'image'         => 'nullable|image|max:2048',
        ]);

        // Jika ada upload image (opsional)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                                    ->store('projects', 'public');
        }

        Project::create($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek berhasil ditambahkan.');
    }

    /**
     * Form edit proyek.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update data proyek.
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price_per_piece'  => 'required|numeric|min:0',
            'quantity'         => 'required|integer|min:1',
            'profit'           => 'required|numeric|min:0',
            'deadline'         => 'required|date|after_or_equal:today',
            'status'           => 'required|in:' . Project::STATUS_ACTIVE . ',' . Project::STATUS_INACTIVE,
            // 'image'         => 'nullable|image|max:2048',
        ]);

        // Handle replacement image jika ada
        if ($request->hasFile('image')) {
            // hapus file lama jika ada
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = $request->file('image')
                                    ->store('projects', 'public');
        }

        $project->update($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    /**
     * Hapus proyek.
     */
    public function destroy(Project $project)
    {
        // jika ada image, hapus dulu
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }


    /**
     * Display a listing of investments (proyek yang di-investasikan).
     */
    public function invested()
    {
        // eager-load investor & project relations
        $investments = Investment::with(['investor', 'project'])
                           ->orderBy('id', 'asc')
                           ->get();

        return view('admin.projects.invested', compact('investments'));
    }

    /**
     * Approve a specific investment.
     */
    public function approveInvestment(Investment $investment)
    {
        $investment->update(['approved' => true]);

        return redirect()
            ->route('admin.projects.invested')
            ->with('success', 'Investasi ID #' . $investment->id . ' berhasil di-approve.');
    }
}
