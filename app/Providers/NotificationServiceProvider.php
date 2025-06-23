<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Menggunakan View Composer untuk membagikan data notifikasi ke view tertentu
        View::composer('layouts.navigation', function ($view) {
            if (Auth::check()) {
                /** @var \App\Models\User $user */ // <-- INI ADALAH PERBAIKANNYA
                $user = Auth::user();
                
                // Ambil 5 notifikasi teratas yang belum dibaca
                $unreadNotifications = $user->unreadNotifications()->take(5)->get();
                $notificationCount = $user->unreadNotifications()->count();

                $view->with([
                    'unreadNotifications' => $unreadNotifications,
                    'notificationCount' => $notificationCount
                ]);
            }
        });
    }
}
