<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TailorProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QcController extends Controller
{
    /**
     * Menampilkan daftar semua laporan progres dari penjahit yang perlu diperiksa (QC).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua laporan progres dengan relasi yang dibutuhkan.
        // Eager loading ('with') sangat penting untuk performa.
        $progressReports = TailorProgress::with([
            'assignment.project', // Laporan -> Tugas -> Proyek
            'assignment.tailor.user', // Laporan -> Tugas -> Penjahit -> User
            'qcAdmin' // Pastikan baris ini ada
        ])
        ->orderBy('date', 'desc') // Tampilkan yang terbaru di atas
        ->paginate(15); // Gunakan paginasi agar halaman tidak berat

        return view('admin.qc.index', compact('progressReports'));
    }

    /**
     * Memproses dan menyimpan hasil pemeriksaan QC dari admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TailorProgress  $progress
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request, TailorProgress $progress)
    {
        // 1. Validasi Input dari Form QC
        $validated = $request->validate([
            'accepted_qty' => 'required|integer|min:0',
            'rejected_qty' => 'required|integer|min:0',
            'qc_notes' => 'nullable|string|max:1000',
        ]);

        // 2. Validasi Logika: Jumlah diterima + ditolak harus sama dengan yang dilaporkan.
        $totalChecked = $validated['accepted_qty'] + $validated['rejected_qty'];
        
        if ($totalChecked !== $progress->quantity_done) {
            return back()->with('error', "Total jumlah (Diterima + Ditolak = {$totalChecked}) tidak sesuai dengan jumlah yang dilaporkan ({$progress->quantity_done}).");
        }

        // 3. Update data laporan progres dengan hasil QC.
        $progress->update([
            'status' => 'approved', // Anggap 'approved' berarti sudah diperiksa.
            'accepted_qty' => $validated['accepted_qty'],
            'rejected_qty' => $validated['rejected_qty'],
            'qc_notes' => $validated['qc_notes'],
            'qc_checked_at' => now(),
            'qc_admin_id' => Auth::id(), // ID admin yang sedang login
        ]);

        // 4. Redirect kembali dengan pesan sukses.
        return redirect()->route('admin.qc.index')->with('success', 'Laporan progres berhasil diproses.');
    }
}
