<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse; // <-- Import untuk tipe data
use Illuminate\View\View; // <-- Import untuk tipe data

class DashboardController extends Controller
{
    /**
     * Menangani permintaan masuk untuk dashboard penjahit.
     *
     * Memeriksa apakah profil penjahit sudah lengkap. Jika belum,
     * akan diarahkan ke halaman edit profil.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        // Dapatkan data pengguna yang sedang login
        $user = Auth::user();

        // Periksa apakah pengguna memiliki data penjahit terkait menggunakan relasi 'tailor'
        if (!$user->tailor) {
            // Jika TIDAK ADA data penjahit, arahkan ke halaman edit profil
            // dengan menyertakan pesan peringatan.
            return redirect()->route('penjahit.profile.edit')
                ->with('warning', 'Harap lengkapi profil penjahit Anda terlebih dahulu untuk melanjutkan.');
        }

        // Jika data penjahit SUDAH ADA, tampilkan halaman dashboard penjahit.
        return view('penjahit.dashboard');
    }
}
