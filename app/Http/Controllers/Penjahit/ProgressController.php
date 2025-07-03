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
     * Menyimpan laporan progres baru dari penjahit.
     */
    public function store(Request $request, ProjectTailor $assignment)
    {
        // 1. Validasi input dasar
        $validated = $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes'         => 'nullable|string',
            'date'          => 'required|date',
        ]);

        // ====================================================================
        // AWAL PERBAIKAN: Logika validasi yang lebih akurat
        // ====================================================================

        // 2. Validasi Logika Bisnis
        
        // Langkah A: Hitung jumlah yang sudah final diterima oleh QC.
        $totalAccepted = $assignment->progress()->sum('accepted_qty');

        // Langkah B: Hitung kuota yang tersisa berdasarkan yang sudah diterima.
        $remainingAfterAcceptance = $assignment->assigned_qty - $totalAccepted;

        // Langkah C: Hitung berapa banyak yang sudah dilaporkan tapi masih menunggu QC.
        // Pengecualian 'whereDate' diperlukan untuk kasus update-or-create pada hari yang sama.
        $reportDate = Carbon::parse($validated['date'])->startOfDay();
        $totalPending = $assignment->progress()
            ->where('status', 'pending')
            ->whereDate('date', '!=', $reportDate)
            ->sum('quantity_done');

        // Langkah D: Kuota yang *sebenarnya* bisa dilaporkan sekarang.
        $maxAllowedToReport = $remainingAfterAcceptance - $totalPending;

        // Langkah E: Validasi input baru terhadap kuota yang sebenarnya.
        if ($validated['quantity_done'] > $maxAllowedToReport) {
            return back()
                ->withInput()
                ->with('error', "Laporan Anda ({$validated['quantity_done']} pcs) melebihi sisa kuota yang tersedia. Anda hanya bisa melaporkan maksimal {$maxAllowedToReport} pcs lagi.");
        }
        
        // ====================================================================
        // AKHIR PERBAIKAN
        // ====================================================================
        
        // 3. Simpan atau perbarui data.
        $assignment->progress()->updateOrCreate(
            [
                'date' => $reportDate,
            ],
            [
                'quantity_done' => $validated['quantity_done'],
                'notes'         => $validated['notes'],
                'status'        => 'pending', // Selalu set ke pending saat ada laporan/update baru
                'accepted_qty'  => null,
                'rejected_qty'  => null,
                'qc_notes'      => null,
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
        return view('penjahit.progress.edit', compact('progress'));
    }

    /**
     * Memperbarui data progres yang sudah ada.
     */
    public function update(Request $request, TailorProgress $progress)
    {
        // 1. Validasi input dasar
        $validated = $request->validate([
            'quantity_done' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        // 2. Validasi Logika Bisnis (menggunakan logika yang sama dengan 'store')
        $assignment = $progress->assignment;
        
        $totalAccepted = $assignment->progress()->sum('accepted_qty');
        $remainingAfterAcceptance = $assignment->assigned_qty - $totalAccepted;

        // Pengecualian 'where id' diperlukan agar tidak menghitung laporan yang sedang diedit ini.
        $totalPending = $assignment->progress()
            ->where('status', 'pending')
            ->where('id', '!=', $progress->id)
            ->sum('quantity_done');
            
        $maxAllowedToReport = $remainingAfterAcceptance - $totalPending;

        if ($validated['quantity_done'] > $maxAllowedToReport) {
            return back()
                ->withInput()
                ->with('error', "Laporan Anda ({$validated['quantity_done']} pcs) melebihi sisa kuota yang tersedia. Anda hanya bisa memasukkan maksimal {$maxAllowedToReport} pcs untuk entri ini.");
        }

        // 3. Eksekusi pembaruan data
        $progress->update([
            'quantity_done' => $validated['quantity_done'],
            'notes'         => $validated['notes'],
            'status'        => 'pending', // Reset status ke pending karena ada perubahan
            'accepted_qty'  => null,
            'rejected_qty'  => null,
            'qc_notes'      => null,
        ]);

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
