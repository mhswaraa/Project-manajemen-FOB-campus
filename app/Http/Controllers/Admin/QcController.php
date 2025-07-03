<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TailorProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QcController extends Controller
{
    /**
     * PERBAIKAN: Method ini diubah untuk menangani logika tab.
     */
    public function index(Request $request)
    {
        // Ambil tab yang aktif dari URL, default ke 'pending'
        $tab = $request->input('tab', 'pending');

        $pendingProgress = collect();
        $approvedProgress = collect();

        // Hanya ambil data yang diperlukan untuk tab yang aktif
        if ($tab === 'pending') {
            $pendingProgress = TailorProgress::where('status', 'pending')
                ->with(['assignment.project', 'assignment.tailor.user'])
                ->latest('date')
                ->get();
        } else { // tab === 'history'
            $approvedProgress = TailorProgress::where('status', 'approved')
                ->with(['assignment.project', 'assignment.tailor.user', 'qcAdmin'])
                ->latest('qc_checked_at')
                ->paginate(15);
        }

        // Kirim semua variabel yang dibutuhkan ke view, termasuk variabel $tab
        return view('admin.qc.index', compact('pendingProgress', 'approvedProgress', 'tab'));
    }

    /**
     * Menampilkan halaman detail untuk satu laporan progres untuk diproses.
     */
    public function show(TailorProgress $progress)
    {
        if ($progress->status !== 'pending') {
            return redirect()->route('admin.qc.index')->with('error', 'Laporan ini sudah diproses sebelumnya.');
        }
        
        $progress->load(['assignment.project', 'assignment.tailor.user']);
        return view('admin.qc.show', compact('progress'));
    }

    /**
     * Memproses dan menyimpan hasil pemeriksaan QC dari admin.
     */
    public function process(Request $request, TailorProgress $progress)
    {
        $validated = $request->validate([
            'accepted_qty' => 'required|integer|min:0',
            'rejected_qty' => 'required|integer|min:0',
            'qc_notes' => 'nullable|string|max:1000',
        ]);

        $totalChecked = $validated['accepted_qty'] + $validated['rejected_qty'];
        if ($totalChecked != $progress->quantity_done) {
            return back()->withInput()->with('error', "Total jumlah (Diterima + Ditolak = {$totalChecked}) tidak sesuai dengan jumlah yang dilaporkan ({$progress->quantity_done}).");
        }

        $progress->update([
            'status' => 'approved',
            'accepted_qty' => $validated['accepted_qty'],
            'rejected_qty' => $validated['rejected_qty'],
            'qc_notes' => $validated['qc_notes'],
            'qc_checked_at' => now(),
            'qc_admin_id' => Auth::id(),
        ]);

        $assignment = $progress->assignment;
        $totalAcceptedOnAssignment = $assignment->progress()->where('status', 'approved')->sum('accepted_qty');
        if ($totalAcceptedOnAssignment >= $assignment->assigned_qty) {
            $assignment->update(['status' => 'completed']);
        }

        return redirect()->route('admin.qc.index')->with('success', 'Laporan progres berhasil diproses.');
    }
}
