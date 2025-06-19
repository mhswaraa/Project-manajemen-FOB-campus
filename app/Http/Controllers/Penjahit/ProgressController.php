<?php
// Path: app/Http/Controllers/Penjahit/ProgressController.php
namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use App\Models\TailorProgress;      // pastikan ini model yang Anda pakai
use App\Models\ProjectTailor;       // model assignment
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Simpan update progress harian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProjectTailor  $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, ProjectTailor $assignment)
    {
        // 1) Pastikan penjahit yang login adalah pemilik assignment
        $tailor = Auth::user()->tailor;
        if (!$tailor || $assignment->tailor_id !== $tailor->tailor_id) {
            abort(403, 'Anda tidak berwenang memperbarui progress tugas ini.');
        }

        // 2) Validasi input
        $validated = $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes'         => 'nullable|string|max:500',
        ]);

        // 3) Cek total progress sejauh ini (sum per-hari sebelumnya)
        $sumSoFar = $assignment->progress()->sum('quantity_done');
        $assigned = $assignment->assigned_qty;

        if ($sumSoFar + $validated['quantity_done'] > $assigned) {
            return back()
                   ->with('error', 'Total keseluruhan progress ('.$sumSoFar.' + '
                          .$validated['quantity_done'].') melebihi jatah tugas ('.$assigned.').');
        }

        // 4) Simpan atau update record per assignment+date
        TailorProgress::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'date'          => now()->toDateString(),
            ],
            [
                'quantity_done' => $validated['quantity_done'],
                'notes'         => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success','Progress untuk tanggal '.now()->toDateString().' berhasil disimpan.');
    }
}
