<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        // ambil semua proyek terbaru
        $projects = Project::latest()->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        // validasi
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'budget'   => 'required|numeric|min:0',
            'deadline' => 'required|date|after_or_equal:today',
            'image'    => 'nullable|image|max:2048',
            'status'   => 'required|in:' . Project::STATUS_ACTIVE . ',' . Project::STATUS_INACTIVE,
        ]);

        // upload image jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projects','public');
        }

        Project::create($data);

        return redirect()->route('admin.projects.index')
                         ->with('success','Proyek berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'budget'   => 'required|numeric|min:0',
            'deadline' => 'required|date|after_or_equal:today',
            'image'    => 'nullable|image|max:2048',
            'status'   => 'required|in:pending,on_progress,completed',
        ]);

        if ($request->hasFile('image')) {
            // hapus file lama
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = $request->file('image')->store('projects','public');
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')
                         ->with('success','Proyek berhasil di‑update.');
    }

    public function destroy(Project $project)
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }
        $project->delete();

        return redirect()->route('admin.projects.index')
                         ->with('success','Proyek berhasil di‑hapus.');
    }
}
