<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CeoController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PenjahitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini kamu daftarkan route–route web.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// (Optional) Dashboard umum, bisa dialihkan per-role nanti
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
         ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
         ->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ───────────────────────────────────────────────────────────────
// ✨ Tambahan untuk Routing per Role
// ───────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])
         ->name('admin.dashboard');
    // ... tambahkan route–route Admin lainnya di sini ...
});

Route::middleware(['auth', 'role:ceo'])->group(function () {
    Route::get('/ceo', [CeoController::class, 'index'])
         ->name('ceo.dashboard');
    // ... route–route CEO ...
});

Route::middleware(['auth', 'role:investor'])->group(function () {
    Route::get('/investor', [InvestorController::class, 'index'])
         ->name('investor.dashboard');
    // ... route–route Investor ...
});

Route::middleware(['auth', 'role:penjahit'])->group(function () {
    Route::get('/penjahit', [PenjahitController::class, 'index'])
         ->name('penjahit.dashboard');
    // ... route–route Penjahit Borongan ...
});
