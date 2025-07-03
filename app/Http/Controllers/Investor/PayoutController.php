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
        // 1. Dapatkan investor yang sedang login
        $investor = Auth::user()->investor;

        // 2. Ambil data payout melalui relasi investor
        //    dan urutkan berdasarkan kolom 'paid_at' yang benar.
        $payouts = $investor->payouts()
                            ->with('investment.project') // Eager load untuk efisiensi
                            ->latest('paid_at') // <-- INI PERBAIKANNYA (latest() adalah shortcut untuk orderBy('paid_at', 'desc'))
                            ->paginate(10);

        // 3. Kirim data ke view
        return view('investor.payouts.index', compact('payouts'));
    }
}
