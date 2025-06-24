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
use App\Http\Controllers\Admin\ReportController      as AdminReportController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;

// Investor controllers
use App\Http\Controllers\Investor\DashboardController  as InvestorDashboardController;
use App\Http\Controllers\Investor\ProjectController    as InvestorProjectController;
use App\Http\Controllers\Investor\InvestmentController as InvestorInvestmentController;
use App\Http\Controllers\Investor\ProfileController    as InvestorProfileController;
use App\Http\Controllers\Investor\PayoutController as InvestorPayoutController;

// Penjahit controllers
use App\Http\Controllers\Penjahit\DashboardController  as PenjahitDashboardController;
use App\Http\Controllers\Penjahit\ProjectController    as PenjahitProjectController;
use App\Http\Controllers\Penjahit\TaskController       as PenjahitTaskController;
use App\Http\Controllers\Penjahit\ProgressController   as PenjahitProgressController;
use App\Http\Controllers\Penjahit\ProfileController    as PenjahitProfileController;
use App\Http\Controllers\Penjahit\PayrollController as PenjahitPayrollController;
use App\Http\Controllers\Penjahit\InvoiceController as PenjahitInvoiceController;

// CEO controllers
use App\Http\Controllers\Ceo\ReportController as CeoReportController;

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
    // … (bisa ditambah update/delete jika perlu) …
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

    // 1.b) Approve Investasi (per-investment)
    Route::post('projects/invested/{investment}/approve', [AdminProjectController::class, 'approveInvestment'])
         ->name('projects.invested.approve');

    // 2) Manajemen Investor (index, create, store, edit, update, destroy)
    Route::resource('investors', AdminInvestorController::class)
         ->except(['show']);

    // 3) Manajemen Penjahit (index, create, store, edit, update, destroy)
    Route::resource('penjahits', AdminPenjahitController::class)
         ->except(['show']);
     
     // TAMBAHKAN ROUTE BARU DI SINI
    Route::get('projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');


      // 4) MANAJEMEN SPESIALISASI (BARU)
    Route::resource('specializations', \App\Http\Controllers\Admin\SpecializationController::class)
         ->except(['show', 'create', 'edit']);
     

         // 5) HALAMAN LAPORAN (BARU)
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');

     // GANTI RUTE PAYROLL LAMA DENGAN INI
     Route::get('invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::post('invoices/{invoice}/pay', [AdminInvoiceController::class, 'pay'])->name('invoices.pay');

     // 7) PEMBAYARAN PROFIT INVESTOR (BARU)
    Route::get('payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::post('payouts/{investment}', [AdminPayoutController::class, 'store'])->name('payouts.store');
});


/*
|--------------------------------------------------------------------------
| CEO Routes
|--------------------------------------------------------------------------
*/
// Ganti rute lama dengan grup rute ini
Route::middleware(['auth','role:ceo'])->prefix('ceo')->name('ceo.')->group(function () {
    // Rute dasbor utama tetap ditangani oleh DashboardController
    // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rute baru untuk laporan
    Route::get('reports/investor-cohort', [CeoReportController::class, 'investorCohort'])->name('reports.investor-cohort');

     // Rute baru untuk papan peringkat produksi
    Route::get('reports/production-leaderboard', [CeoReportController::class, 'productionLeaderboard'])->name('reports.production-leaderboard');

     // Rute baru untuk peramalan arus kas
    Route::get('reports/cash-flow-forecast', [CeoReportController::class, 'cashFlowForecast'])->name('reports.cash-flow-forecast');
});



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

    // 2) Daftar Proyek Aktif
    Route::get('projects', [InvestorProjectController::class, 'index'])
         ->name('projects.index');

    // 3) Invest Form per Proyek
    Route::get('projects/{project}/invest', [InvestorProjectController::class, 'create'])
         ->name('projects.invest');
    Route::post('projects/{project}/invest', [InvestorProjectController::class, 'store'])
         ->name('projects.store');

    // 4) Investasi Saya (list + CRUD bagi investor sendiri)
    Route::get('investments', [InvestorInvestmentController::class, 'index'])
         ->name('investments.index');
    Route::get('investments/{investment}', [InvestorInvestmentController::class, 'show'])
         ->name('investments.show');
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

     // 6) RIWAYAT PEMBAYARAN PROFIT (BARU)
    Route::get('payouts', [InvestorPayoutController::class, 'index'])->name('payouts.index');
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
    Route::get('dashboard', [PenjahitDashboardController::class, 'index'])->name('dashboard');

    // 2) Daftar Proyek Tersedia
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
    Route::get('tasks/{assignment}', [PenjahitTaskController::class, 'show'])
         ->name('tasks.show');

    // 4) Update Progress Harian
    Route::post('tasks/{assignment}/progress', [PenjahitProgressController::class, 'store'])
         ->name('tasks.progress.store');

    // 5) Profil Penjahit (view & submit)
    Route::get('profile', [PenjahitProfileController::class, 'index'])
     ->name('profile.index'); // <-- Ubah 'profile' menjadi 'profile.index'

    Route::post('profile', [PenjahitProfileController::class, 'storeOrUpdate'])
         ->name('profile.update');
         
     Route::post('profile/portfolio', [PenjahitProfileController::class, 'addPortfolio'])->name('profile.portfolio.add');

     Route::delete('profile/portfolio/{portfolio}', [PenjahitProfileController::class, 'deletePortfolio'])->name('profile.portfolio.delete');

     // 6) RIWAYAT GAJI (BARU)
    Route::get('payrolls', [PenjahitPayrollController::class, 'index'])->name('payrolls.index');

    // 7) MANAJEMEN INVOICE (BARU)
    Route::get('invoices', [PenjahitInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [PenjahitInvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [PenjahitInvoiceController::class, 'store'])->name('invoices.store');
});


// Register route (only Admin)
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('register', [RegisteredUserController::class, 'create'])
         ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});
