<?php
// Path: app/Http/Controllers/Penjahit/TaskController.php
namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use App\Models\ProjectTailor;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Daftar semua tugas (assignments) milik penjahit.
     */
     public function index()
    {
        $tailorId = Auth::user()->tailor->tailor_id;
        $assignments = ProjectTailor::with('project', 'progress')
                           ->where('tailor_id', $tailorId)
                           ->get();
        return view('penjahit.tasks.index', compact('assignments'));
    }

    /**
     * Detail satu tugas, termasuk history progress.
     */
     public function show(ProjectTailor $assignment)
    {
        // Pastikan assignment milik tailor yang login
        $tailorId = Auth::user()->tailor->tailor_id;
        if ($assignment->tailor_id !== $tailorId) {
            abort(403);
        }

        // eager load progress
        $assignment->load('project', 'progress');
        return view('penjahit.tasks.show', compact('assignment'));
    }
    public function destroy(ProjectTailor $assignment)
    {
        // 1. Otorisasi: Pastikan penjahit hanya bisa menghapus tugasnya sendiri.
        if ($assignment->tailor_id !== Auth::user()->tailor->tailor_id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        // 2. Validasi Bisnis: Cek apakah sudah ada progres yang dilaporkan.
        // `progress()` adalah nama relasi dari model ProjectTailor ke TailorProgress.
        // Jika nama relasi Anda berbeda, sesuaikan.
        if ($assignment->progress()->exists()) {
            return redirect()
                ->route('penjahit.tasks.index')
                ->with('error', 'Tugas tidak bisa dibatalkan karena progres sudah dilaporkan.');
        }

        // 3. Eksekusi: Hapus assignment jika validasi lolos.
        $assignment->delete();

        // 4. Redirect dengan pesan sukses.
        return redirect()
            ->route('penjahit.tasks.index')
            ->with('success', 'Tugas berhasil dibatalkan dan dikembalikan ke daftar proyek.');
    }
}
