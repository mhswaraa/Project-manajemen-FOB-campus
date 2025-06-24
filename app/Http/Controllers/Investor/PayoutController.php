<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    /**
     * Menampilkan riwayat pembayaran profit untuk investor yang sedang login.
     */
    public function index()
    {
        $investor = Auth::user()->investor;

        if (!$investor) {
            return redirect()->route('investor.profile')->with('warning', 'Harap lengkapi profil Anda terlebih dahulu.');
        }

        // Ambil semua payout melalui relasi yang akan kita buat di model Investor
        $payouts = $investor->payouts()
                           ->with(['investment.project', 'processor']) // Eager load relasi
                           ->latest('payment_date')
                           ->paginate(15);

        return view('investor.payouts.index', compact('payouts'));
    }
}
