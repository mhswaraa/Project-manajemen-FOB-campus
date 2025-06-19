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
}
