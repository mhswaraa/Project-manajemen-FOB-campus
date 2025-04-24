<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Investment;

class InvestmentController extends Controller
{
    /**
     * Tampilkan list investasi milik investor,
     * lengkap dengan proyek & progress penjahit.
     */
    public function index()
    {
        // Ambil investor_id dari user auth
        $investorId = Auth::user()->investor->investor_id;

        // Query semua investasi investor ini,
        // eager-load proyek dan progress produksi
        $investments = Investment::with([
                'project', 
                'project.productionProgress'  // relasi ke progress penjahit
            ])
            ->where('investor_id', $investorId)
            ->latest('created_at')
            ->get();

        return view('investor.investments.index', compact('investments'));
    }
}
