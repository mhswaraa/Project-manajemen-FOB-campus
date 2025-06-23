<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investor;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil investor.
     */
    public function index()
    {
        $user = Auth::user();
        $investor = $user->investor;

        // Data untuk kartu ringkasan portofolio
        $totalInvested = 0;
        $projectsCount = 0;
        $estimatedProfit = 0;

        if ($investor) {
            $totalInvested = $investor->investments()->where('approved', true)->sum('amount');
            $projectsCount = $investor->investments()->where('approved', true)->distinct('project_id')->count();
            
            // Kalkulasi estimasi profit
            $estimatedProfit = DB::table('investments')
                ->join('projects', 'investments.project_id', '=', 'projects.id')
                ->where('investments.investor_id', $investor->investor_id)
                ->where('investments.approved', true)
                ->sum(DB::raw('investments.qty * projects.profit'));
        }
        
        return view('investor.profile.index', compact(
            'user', 
            'investor',
            'totalInvested',
            'projectsCount',
            'estimatedProfit'
        ));
    }

    /**
     * Menyimpan atau memperbarui profil investor.
     */
    public function storeOrUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update(['name' => $request->name]);

        Investor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'         => $request->phone,
                'name'          => $request->name,
                'email'         => $user->email,
                'registered_at' => now(),
            ]
        );

        // PERBAIKAN DI SINI: Ubah nama rute menjadi 'investor.profile'
        return redirect()->route('investor.profile')
                         ->with('success', 'Profil Anda telah berhasil diperbarui.');
    }
}
