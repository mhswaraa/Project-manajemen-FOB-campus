<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CeoController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PenjahitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;

// import untuk role admin
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\PenjahitController as AdminPenjahitController;

// import untuk role investor
use App\Http\Controllers\Investor\DashboardController      as InvestorDashboardController;
use App\Http\Controllers\Investor\ProjectController        as InvestorProjectController;
use App\Http\Controllers\Investor\InvestmentController     as InvestorInvestmentController;
use App\Http\Controllers\Investor\ProfileController        as InvestorProfileController;

use Illuminate\Support\Facades\Route;

// Welcome
Route::get('/', fn() => view('welcome'));

// Auth (login, password reset, email verif)
require __DIR__.'/auth.php';

// Profile (semua user)
Route::middleware('auth')->group(function(){
    Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');
    // ...
});

// Single entrypoint Dashboard
Route::middleware(['auth','verified'])
     ->get('/dashboard',[DashboardController::class,'index'])
     ->name('dashboard');

// Halaman‐halaman per role

// Admin
Route::middleware(['auth','role:admin'])
     ->prefix('admin')->name('admin.')
     ->group(function(){
         // Manajemen Proyek
         Route::resource('projects', AdminProjectController::class)
              ->only(['index','store','edit','update','destroy']);
              // Manajemen Investor
         Route::resource('investors', AdminInvestorController::class)
         ->except(['show']);
         // Manajemen Investor
         Route::resource('penjahits', AdminPenjahitController::class)
          ->except(['show']);
     });

Route::middleware(['auth','role:ceo'])
     ->get('/ceo',[CeoController::class,'index'])
     ->name('ceo.dashboard');

// Investor
// Route::middleware(['auth','role:investor'])->group(function(){
//      // Dashboard investor
//      Route::get('/investor',[InvestorController::class,'index'])
//           ->name('investor.dashboard');
 
//      // Form Create Investor (pre‐fill name/email)
//      Route::get('/investors/create',[InvestorController::class,'create'])
//           ->name('investors.create');
//      // Store Investor ke tabel `investors`
//      Route::post('/investors',[InvestorController::class,'store'])
//           ->name('investors.store');
//  });

Route::prefix('investor')
     ->middleware(['auth','role:investor'])
     ->name('investor.')
     ->group(function(){
         // Dashboard
         Route::get('/dashboard', [InvestorDashboardController::class,'index'])
              ->name('dashboard');

         // 1) Daftar Proyek
         Route::get('/projects', [InvestorProjectController::class,'index'])
              ->name('projects.index');

         // 2) Form Investasi per Proyek
         Route::get('/projects/{project}/invest', [InvestorProjectController::class,'create'])
              ->name('projects.invest');
         Route::post('/projects/{project}/invest', [InvestorProjectController::class,'store'])
              ->name('projects.store');

         // 3) Investasi Saya
         Route::get('/investments', [InvestorInvestmentController::class,'index'])
              ->name('investments.index');

        // Profil Investor (create & update di satu controller)
        Route::get ('/profile', [InvestorProfileController::class,'index'])
        ->name('profile');
               Route::post('/profile', [InvestorProfileController::class,'storeOrUpdate'])
        ->name('profile.update');
});

// Penjahit Borongan
Route::middleware(['auth','role:penjahit'])->group(function(){
     // Dashboard penjahit
     Route::get('/penjahit',[PenjahitController::class,'index'])
          ->name('penjahit.dashboard');

     // Form Create Penjahit (pre‐fill name/email)
     Route::get('/penjahits/create',[PenjahitController::class,'create'])
          ->name('penjahits.create');
     // Store Penjahit ke tabel `penjahits`
     Route::post('/penjahits',[PenjahitController::class,'store'])
          ->name('penjahits.store');
           // Manajemen Penjahit
 });

// Register hanya Admin
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('/register',[RegisteredUserController::class,'create'])->name('register');
    Route::post('/register',[RegisteredUserController::class,'store']);
});
