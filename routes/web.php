<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CeoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Admin controllers
use App\Http\Controllers\Admin\ProjectController      as AdminProjectController;
use App\Http\Controllers\Admin\InvestorController     as AdminInvestorController;
use App\Http\Controllers\Admin\PenjahitController     as AdminPenjahitController;

// Investor controllers
use App\Http\Controllers\Investor\DashboardController  as InvestorDashboardController;
use App\Http\Controllers\Investor\ProjectController    as InvestorProjectController;
use App\Http\Controllers\Investor\InvestmentController as InvestorInvestmentController;
use App\Http\Controllers\Investor\ProfileController    as InvestorProfileController;

// Penjahit controllers
use App\Http\Controllers\Penjahit\DashboardController  as PenjahitDashboardController;
use App\Http\Controllers\Penjahit\ProjectController    as PenjahitProjectController;
use App\Http\Controllers\Penjahit\TaskController       as PenjahitTaskController;
use App\Http\Controllers\Penjahit\ProgressController   as PenjahitProgressController;
use App\Http\Controllers\Penjahit\ProfileController    as PenjahitProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage / welcome
Route::get('/', fn() => view('welcome'));

// Auth (login, register, password reset, email verification)
require __DIR__ . '/auth.php';

// Common profile edit (any authenticated user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');
    // … you can add update/delete here …
});

// Single entrypoint: Dashboard (all roles)
Route::middleware(['auth', 'verified'])
     ->get('/dashboard', [DashboardController::class, 'index'])
     ->name('dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    // 1) Manajemen Proyek (index, store, edit, update, destroy)
    Route::resource('projects', AdminProjectController::class)
         ->only(['index','store','edit','update','destroy']);

    // 1.a) Daftar Investasi (semua record investments)
    Route::get('projects/invested', [AdminProjectController::class, 'invested'])
         ->name('projects.invested');

    // 1.b) Approve Investasi (ubah kolom approved pada investment_id)
    // — gunakan {investment} bukan {project}, agar secara otomatis bind ke App\Models\Investment
    Route::post('projects/invested/{investment}/approve', [AdminProjectController::class, 'approveInvestment'])
         ->name('projects.invested.approve');

    // 2) Manajemen Investor (index, create, store, edit, update, destroy)
    Route::resource('investors', AdminInvestorController::class)
         ->except(['show']);  

    // 3) Manajemen Penjahit (index, create, store, edit, update, destroy)
    Route::resource('penjahits', AdminPenjahitController::class)
         ->except(['show']);  
});


/*
|--------------------------------------------------------------------------
| CEO Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:ceo'])
     ->get('/ceo', [CeoController::class, 'index'])
     ->name('ceo.dashboard');


/*
|--------------------------------------------------------------------------
| INVESTOR Routes
|--------------------------------------------------------------------------
*/
Route::prefix('investor')
     ->middleware(['auth','role:investor'])
     ->name('investor.')
     ->group(function () {

    // 1) Dashboard
    Route::get('dashboard', [InvestorDashboardController::class, 'index'])
         ->name('dashboard');

    // 2) Daftar Proyek (Active)
    Route::get('projects', [InvestorProjectController::class, 'index'])
         ->name('projects.index');

     Route::get('admin/projects/invested', [AdminProjectController::class, 'invested'])
     ->name('admin.projects.invested');

    // 3) Invest Form per Proyek
    Route::get('projects/{project}/invest', [InvestorProjectController::class, 'create'])
         ->name('projects.invest');
    Route::post('projects/{project}/invest', [InvestorProjectController::class, 'store'])
         ->name('projects.store');

    // 4) Investasi Saya
    Route::get('investments', [InvestorInvestmentController::class, 'index'])
         ->name('investments.index');
     // → Tambahkan routes berikut:
    Route::get('investments/{investment}/edit', [InvestorInvestmentController::class, 'edit'])
         ->name('investments.edit');
    Route::put('investments/{investment}', [InvestorInvestmentController::class, 'update'])
         ->name('investments.update');
    Route::delete('investments/{investment}', [InvestorInvestmentController::class, 'destroy'])
         ->name('investments.destroy');

    // 5) Profil Investor (create/update)
    Route::get('profile', [InvestorProfileController::class, 'index'])
         ->name('profile');
    Route::post('profile', [InvestorProfileController::class, 'storeOrUpdate'])
         ->name('profile.update');

     
});


/*
|--------------------------------------------------------------------------
| PENJAHIT Routes
|--------------------------------------------------------------------------
*/
Route::prefix('penjahit')
     ->middleware(['auth','role:penjahit'])
     ->name('penjahit.')
     ->group(function () {

    // 1) Dashboard
    Route::get('dashboard', [PenjahitDashboardController::class, 'index'])
         ->name('dashboard');

    // 2) Daftar Proyek (approved + sisa kuota)
    Route::get('projects', [PenjahitProjectController::class, 'index'])
         ->name('projects.index');

    // Ambil proyek / form assignment
    Route::get('projects/{project}/take', [PenjahitProjectController::class, 'create'])
         ->name('projects.take');
    Route::post('projects/{project}/take', [PenjahitProjectController::class, 'store'])
         ->name('projects.store');

    // 3) Tugas Saya (list + detail)
    Route::get('tasks', [PenjahitTaskController::class, 'index'])
         ->name('tasks.index');
    // menampilkan detail satu tugas (assignment) 
    Route::get('tasks/{assignment}', [PenjahitTaskController::class, 'show'])
         ->name('tasks.show');

    // 4) Update Progress Harian
    Route::post('tasks/{assignment}/progress', [PenjahitProgressController::class, 'store'])
         ->name('tasks.progress.store');

    // 5) Profil Penjahit (view & submit)
    Route::get('profile', [PenjahitProfileController::class, 'index'])
         ->name('profile');
    Route::post('profile', [PenjahitProfileController::class, 'storeOrUpdate'])
         ->name('profile.update');
});


// Register (only Admin can create users)
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('register', [RegisteredUserController::class, 'create'])
         ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});
