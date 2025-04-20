<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CeoController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PenjahitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\PenjahitController as AdminPenjahitController;

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
Route::middleware(['auth','role:investor'])->group(function(){
     // Dashboard investor
     Route::get('/investor',[InvestorController::class,'index'])
          ->name('investor.dashboard');
 
     // Form Create Investor (pre‐fill name/email)
     Route::get('/investors/create',[InvestorController::class,'create'])
          ->name('investors.create');
     // Store Investor ke tabel `investors`
     Route::post('/investors',[InvestorController::class,'store'])
          ->name('investors.store');
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
