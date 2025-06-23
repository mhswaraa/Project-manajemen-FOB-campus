<?php

namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /**
     * Menampilkan riwayat penggajian untuk penjahit yang sedang login.
     */
    public function index()
    {
        $tailor = Auth::user()->tailor;

        if (!$tailor) {
            // Jika profil penjahit belum ada, redirect dengan pesan
            return redirect()->route('penjahit.profile.index')
                             ->with('warning', 'Harap lengkapi profil Anda untuk melihat riwayat gaji.');
        }
        
        // Ambil data payroll milik penjahit ini, urutkan dari yang terbaru, dan paginasi
        $payrolls = $tailor->payrolls()
                           ->with('processor') // Eager load data admin yang memproses
                           ->latest('payment_date')
                           ->paginate(10);

        return view('penjahit.payrolls.index', compact('payrolls'));
    }
}
