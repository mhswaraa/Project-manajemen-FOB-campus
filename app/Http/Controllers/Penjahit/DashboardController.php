<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\ProjectTailor;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dasbor untuk peran Penjahit.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pastikan profil penjahit sudah lengkap sebelum menampilkan dasbor.
        if (!$user->tailor) {
            return redirect()->route('penjahit.profile.index')
                ->with('warning', 'Harap lengkapi profil penjahit Anda terlebih dahulu untuk melanjutkan.');
        }
        
        // Ambil ID penjahit dari relasi
        $tailorId = $user->tailor->tailor_id;

        // Ambil semua tugas (assignments) milik penjahit ini,
        // beserta data proyek dan progresnya untuk ditampilkan.
        $assignments = ProjectTailor::with(['project', 'progress'])
                           ->where('tailor_id', $tailorId)
                           ->get();

        // Kirim data assignments ke view dasbor penjahit
        return view('penjahit.dashboard', compact('assignments'));
    }
}
