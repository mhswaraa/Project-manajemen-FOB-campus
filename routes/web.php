<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Admin controllers
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\PenjahitController as AdminPenjahitController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SpecializationController as AdminSpecializationController;

// Investor controllers
use App\Http\Controllers\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Investor\ProjectController as InvestorProjectController;
use App\Http\Controllers\Investor\InvestmentController as InvestorInvestmentController;
use App\Http\Controllers\Investor\ProfileController as InvestorProfileController;
use App\Http\Controllers\Investor\PayoutController as InvestorPayoutController;

// Penjahit controllers
use App\Http\Controllers\Penjahit\DashboardController as PenjahitDashboardController;
use App\Http\Controllers\Penjahit\ProjectController as PenjahitProjectController;
use App\Http\Controllers\Penjahit\TaskController as PenjahitTaskController;
use App\Http\Controllers\Penjahit\ProgressController as PenjahitProgressController;
use App\Http\Controllers\Penjahit\ProfileController as PenjahitProfileController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
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
/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1) Manajemen User (Fitur baru yang terpusat)
    Route::resource('users', UserController::class);

    // --- PERBAIKAN ---
    // 2) Manajemen Proyek
    // Rute spesifik harus didefinisikan SEBELUM rute resource yang umum.
    Route::get('projects/invested', [AdminProjectController::class, 'invested'])->name('projects.invested');
    Route::post('projects/invested/{investment}/approve', [AdminProjectController::class, 'approveInvestment'])->name('projects.invested.approve');
    
    // Rute resource untuk proyek. Duplikasi telah dihapus dari sini.
    Route::resource('projects', AdminProjectController::class)->except(['create']);
    // --- AKHIR PERBAIKAN ---

    // 3) Manajemen Investor
    Route::resource('investors', AdminInvestorController::class)->except(['show']);

    // 4) Manajemen Penjahit
    Route::resource('penjahits', AdminPenjahitController::class)->except(['show']);
    
    // 5) Manajemen Spesialisasi
    Route::resource('specializations', AdminSpecializationController::class)->except(['show', 'create', 'edit']);
    
    // 6) Halaman Laporan
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');

    // 7) Manajemen Invoice
    Route::get('invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::post('invoices/{invoice}/pay', [AdminInvoiceController::class, 'pay'])->name('invoices.pay');
    Route::get('invoices/{invoice}/download', [AdminInvoiceController::class, 'downloadPDF'])->name('invoices.download');

    // 8) Pembayaran Profit Investor (Payouts)
    Route::get('payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::post('payouts/process', [AdminPayoutController::class, 'process'])->name('payouts.process');
    Route::get('payouts/{payout}', [AdminPayoutController::class, 'show'])->name('payouts.show');
    Route::get('payouts/{payout}/receipt', [AdminPayoutController::class, 'downloadReceipt'])->name('payouts.receipt');
    
    // ================== TAMBAHKAN BARIS INI ==================
    Route::get('payouts/{payout}/pdf', [AdminPayoutController::class, 'downloadPdf'])->name('payouts.pdf');
    // =======================================================

     // AWAL DARI ROUTE BARU UNTUK QC
    Route::get('qc', [App\Http\Controllers\Admin\QcController::class, 'index'])->name('qc.index');
    Route::post('qc/process/{progress}', [App\Http\Controllers\Admin\QcController::class, 'process'])->name('qc.process');
    Route::get('qc/{progress}', [App\Http\Controllers\Admin\QcController::class, 'show'])->name('qc.show');
    // ... rute qc.index dan qc.process ...
});


/*
|--------------------------------------------------------------------------
| CEO Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:ceo'])->prefix('ceo')->name('ceo.')->group(function () {
    Route::get('reports/investor-cohort', [CeoReportController::class, 'investorCohort'])->name('reports.investor-cohort');
    Route::get('reports/production-leaderboard', [CeoReportController::class, 'productionLeaderboard'])->name('reports.production-leaderboard');
    Route::get('reports/cash-flow-forecast', [CeoReportController::class, 'cashFlowForecast'])->name('reports.cash-flow-forecast');
});


/*
|--------------------------------------------------------------------------
| INVESTOR Routes
|--------------------------------------------------------------------------
*/
// PERBAIKAN: Menggabungkan rute investor ke dalam satu grup induk
Route::middleware(['auth', 'role:investor'])->prefix('investor')->name('investor.')->group(function () {
    // Rute-rute yang dapat diakses kapan saja (untuk melengkapi profil)
    Route::get('profile', [InvestorProfileController::class, 'index'])->name('profile'); 
    Route::post('profile', [InvestorProfileController::class, 'storeOrUpdate'])->name('profile.update');
    Route::get('profile/download-mou', [InvestorProfileController::class, 'downloadMOU'])->name('profile.downloadMOU');
    Route::post('profile/upload-mou', [InvestorProfileController::class, 'uploadMOU'])->name('profile.uploadMOU');

    // Rute-rute yang hanya bisa diakses SETELAH profil lengkap
    Route::middleware('profile.complete')->group(function () {
        Route::get('dashboard', [InvestorDashboardController::class, 'index'])->name('dashboard');
        Route::get('projects', [InvestorProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}/invest', [InvestorProjectController::class, 'create'])->name('projects.invest');
        Route::post('projects/{project}/invest', [InvestorProjectController::class, 'store'])->name('projects.store');
        Route::resource('investments', InvestorInvestmentController::class)->except(['create', 'store']);
        Route::get('payouts', [InvestorPayoutController::class, 'index'])->name('payouts.index');
    });
});



/*
|--------------------------------------------------------------------------
| PENJAHIT Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:penjahit'])->prefix('penjahit')->name('penjahit.')->group(function () {
    // Rute profil (dapat diakses kapan saja)
    Route::get('profile', [PenjahitProfileController::class, 'index'])->name('profile');
    Route::post('profile', [PenjahitProfileController::class, 'storeOrUpdate'])->name('profile.update');
    Route::post('profile/portfolio', [PenjahitProfileController::class, 'addPortfolio'])->name('profile.portfolio.add');
    Route::delete('profile/portfolio/{portfolio}', [PenjahitProfileController::class, 'deletePortfolio'])->name('profile.portfolio.delete');

    // Rute lain (memerlukan profil lengkap)
    Route::middleware('profile.complete')->group(function () {
        Route::get('dashboard', [PenjahitDashboardController::class, 'index'])->name('dashboard');
        Route::get('projects', [PenjahitProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}/take', [PenjahitProjectController::class, 'create'])->name('projects.take');
        Route::post('projects/{project}/take', [PenjahitProjectController::class, 'store'])->name('projects.store');
        Route::get('tasks', [PenjahitTaskController::class, 'index'])->name('tasks.index');
        Route::get('tasks/{assignment}', [PenjahitTaskController::class, 'show'])->name('tasks.show');
        // TAMBAHKAN ROUTE DI BAWAH INI
        Route::delete('tasks/{assignment}', [PenjahitTaskController::class, 'destroy'])->name('tasks.destroy');
        Route::post('tasks/{assignment}/progress', [PenjahitProgressController::class, 'store'])->name('tasks.progress.store');
        Route::get('progress/{progress}/edit', [PenjahitProgressController::class, 'edit'])->name('tasks.progress.edit');
        Route::put('progress/{progress}', [PenjahitProgressController::class, 'update'])->name('tasks.progress.update');
        Route::delete('progress/{progress}', [PenjahitProgressController::class, 'destroy'])->name('tasks.progress.destroy');
        Route::get('payrolls', [PenjahitPayrollController::class, 'index'])->name('payrolls.index');
        Route::get('invoices', [PenjahitInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/create', [PenjahitInvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [PenjahitInvoiceController::class, 'store'])->name('invoices.store');
    });
});

// Register route (only Admin)
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});
