<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Investor;
use App\Models\Tailor;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Jika tidak ada user yang login, lanjutkan saja.
        if (!$user) {
            return $next($request);
        }

        // Logika untuk Investor
        if ($user->role === 'investor') {
            // Memastikan profil investor ada, atau membuatnya jika belum ada.
            // Ini juga mengatasi error jika kolom 'phone' tidak punya nilai default.
            $investor = $user->investor()->firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name, 'email' => $user->email, 'phone' => '']
            );

            // Jika nomor telepon kosong, profil dianggap belum lengkap.
            if (empty($investor->phone)) {
                // Kecualikan semua rute yang berhubungan dengan profil untuk menghindari redirect loop.
                if (!$request->routeIs('investor.profile.*')) {
                    return redirect()->route('investor.profile')
                        ->with('warning', 'Harap lengkapi profil Anda (No. Telepon) sebelum dapat melanjutkan.');
                }
            }
        }

        // Logika untuk Penjahit (Tailor)
        if ($user->role === 'penjahit') {
            // Memastikan profil penjahit ada, atau membuatnya jika belum ada.
            $tailor = $user->tailor()->firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name, 'email' => $user->email, 'phone' => '']
            );
            
            // Jika nomor telepon kosong, profil dianggap belum lengkap.
            if (empty($tailor->phone)) {
                 // Kecualikan semua rute yang berhubungan dengan profil penjahit.
                if (!$request->routeIs('penjahit.profile.*')) {
                    return redirect()->route('penjahit.profile')
                        ->with('warning', 'Harap lengkapi profil Anda (No. Telepon) sebelum dapat melanjutkan.');
                }
            }
        }
        
        return $next($request);
    }
}
