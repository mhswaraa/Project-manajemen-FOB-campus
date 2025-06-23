<?php
// Path: app/Http/Controllers/Penjahit/DashboardController.php
namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\ProjectTailor; // 1. Tambahkan use statement ini

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->tailor) {
            return redirect()->route('penjahit.profile.edit')
                ->with('warning', 'Harap lengkapi profil penjahit Anda terlebih dahulu untuk melanjutkan.');
        }
        
        // 2. Ambil ID penjahit
        $tailorId = $user->tailor->tailor_id;

        // 3. Ambil semua tugas (assignments) milik penjahit ini
        $assignments = ProjectTailor::with(['project', 'progress'])
                           ->where('tailor_id', $tailorId)
                           ->get();

        // 4. Kirim variabel $assignments ke view
        return view('penjahit.dashboard', compact('assignments'));
    }
}