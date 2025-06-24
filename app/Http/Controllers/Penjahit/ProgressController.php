<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectTailor;
use App\Models\TailorProgress;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Menyimpan laporan progres harian dari penjahit.
     */
    public function store(Request $request, ProjectTailor $assignment)
    {
        // 1. Validasi Input
        $totalDone = $assignment->progress()->sum('quantity_done');
        $remainingQty = $assignment->assigned_qty - $totalDone;

        $request->validate([
            'quantity_done' => "required|integer|min:1|max:{$remainingQty}",
            'notes'         => 'nullable|string',
        ]);

        // 2. Simpan Progres Baru
        $assignment->progress()->create([
            'date'          => now(),
            'quantity_done' => $request->quantity_done,
            'notes'         => $request->notes,
        ]);

        // 3. Cek apakah tugas sudah selesai
        // Hitung ulang total progres setelah penambahan baru
        $newTotalDone = $assignment->progress()->sum('quantity_done');

        if ($newTotalDone >= $assignment->assigned_qty) {
            // Jika sudah selesai, update record assignment
            $assignment->update([
                'status'       => 'completed',
                'completed_at' => now(), // <-- Kolom ini diisi secara otomatis
            ]);

            return back()->with('success', 'Kerja bagus! Tugas ini telah selesai dan ditandai lunas.');
        }

        return back()->with('success', 'Laporan progres berhasil disimpan.');
    }
}
