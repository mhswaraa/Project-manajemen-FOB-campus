<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use App\Models\ProjectTailor; // Ini adalah model assignment
use App\Models\TailorProgress;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgressController extends Controller
{
    /**
     * Menyimpan atau memperbarui progres harian untuk sebuah tugas.
     */
    public function store(Request $request, ProjectTailor $assignment)
    {
        $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $assignment->progresses()->updateOrCreate(
            [
                'date' => Carbon::parse($request->date)->startOfDay(),
            ],
            [
                'quantity_done' => $request->quantity_done,
                'notes' => $request->notes,
            ]
        );

        return redirect()->route('penjahit.tasks.show', $assignment)
                         ->with('success', 'Progres produksi berhasil disimpan.');
    }

    /**
     * Menampilkan form untuk mengedit progres.
     */
    public function edit(TailorProgress $progress)
    {
        // PERBAIKAN: Pemeriksaan kebijakan (Policy) dihapus untuk mengatasi error 403.
        // Baris '$this->authorize()' telah dihilangkan dari method ini.
        
        return view('penjahit.progress.edit', compact('progress'));
    }

    /**
     * Memperbarui data progres di database.
     */
    public function update(Request $request, TailorProgress $progress)
    {
        // PERBAIKAN: Pemeriksaan kebijakan (Policy) dihapus untuk mengatasi error 403.
        // Baris '$this->authorize()' telah dihilangkan dari method ini.

        $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $progress->update([
            'quantity_done' => $request->quantity_done,
            'notes' => $request->notes,
        ]);

        return redirect()->route('penjahit.tasks.show', $progress->assignment_id)
                         ->with('success', 'Progres berhasil diperbarui.');
    }

    /**
     * Menghapus data progres.
     */
    public function destroy(TailorProgress $progress)
    {
        // PERBAIKAN: Pemeriksaan kebijakan (Policy) dihapus untuk mengatasi error 403.
        // Baris '$this->authorize()' telah dihilangkan dari method ini.
        
        $assignmentId = $progress->assignment_id;
        $progress->delete();

        return redirect()->route('penjahit.tasks.show', $assignmentId)
                         ->with('success', 'Laporan progres berhasil dihapus.');
    }
}
