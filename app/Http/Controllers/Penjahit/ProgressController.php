<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use App\Models\ProjectTailor;
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
        // 1. Validasi input dasar. Nama field kembali menjadi 'date'.
        $validated = $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'date' => 'required|date', // <-- PERBAIKAN: Kembali ke 'date'
        ]);

        $reportDate = Carbon::parse($validated['date'])->startOfDay();

        // 2. Validasi Logika Bisnis
        // Ambil progres dari hari-hari lain
        // PERBAIKAN: Kembali menggunakan kolom 'date'
        $otherDaysProgress = $assignment->progress()
            ->whereDate('date', '!=', $reportDate) // <-- PERBAIKAN
            ->sum('quantity_done');

        $newTotalProgress = $otherDaysProgress + $validated['quantity_done'];

        if ($newTotalProgress > $assignment->assigned_qty) {
            $remainingQty = $assignment->assigned_qty - $otherDaysProgress;
            return back()
                ->withInput()
                ->with('error', "Jumlah total progres ({$newTotalProgress} pcs) melebihi kuota tugas ({$assignment->assigned_qty} pcs). Anda hanya bisa melaporkan maksimal {$remainingQty} pcs lagi.");
        }
        
        // 3. Eksekusi: Simpan atau perbarui data
        // PERBAIKAN: Kembali ke 'progress()' dan menggunakan kolom 'date'
        $assignment->progress()->updateOrCreate(
            [
                'date' => $reportDate, // <-- PERBAIKAN
            ],
            [
                'quantity_done' => $validated['quantity_done'],
                'notes' => $validated['notes'],
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
        // Otorisasi bisa ditambahkan di sini jika perlu
        return view('penjahit.progress.edit', compact('progress'));
    }

    /**
     * Memperbarui data progres di database.
     */
     public function update(Request $request, TailorProgress $progress)
    {
        // 1. Validasi input dasar
        $validated = $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        // 2. Validasi Logika Bisnis: Cek agar total tidak melebihi kuota
        $assignment = $progress->assignment;
        
        // Ambil total progres yang sudah dilaporkan di hari-hari LAIN (tidak termasuk hari yang diedit)
        $otherDaysProgress = $assignment->progress()
            ->where('id', '!=', $progress->id)
            ->sum('quantity_done');
            
        // Hitung total progres JIKA laporan ini diperbarui
        $newTotalProgress = $otherDaysProgress + $validated['quantity_done'];
        
        // Jika total baru melebihi kuota, kembalikan dengan error
        if ($newTotalProgress > $assignment->assigned_qty) {
            $allowedQty = $assignment->assigned_qty - $otherDaysProgress;
            return back()
                ->withInput()
                ->with('error', "Jumlah total progres ({$newTotalProgress} pcs) melebihi kuota tugas ({$assignment->assigned_qty} pcs). Anda hanya bisa memasukkan maksimal {$allowedQty} pcs untuk entri ini.");
        }

        // 3. Eksekusi pembaruan data
        $progress->update($validated);

        return redirect()->route('penjahit.tasks.show', $progress->assignment_id)
                         ->with('success', 'Laporan progres berhasil diperbarui.');
    }

    /**
     * Menghapus data progres.
     */
    public function destroy(TailorProgress $progress)
    {
        $assignmentId = $progress->assignment_id;
        $progress->delete();

        return redirect()->route('penjahit.tasks.show', $assignmentId)
                         ->with('success', 'Laporan progres berhasil dihapus.');
    }
}