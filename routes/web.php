<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\JenisPotonganController;
use App\Http\Controllers\Admin\InputBulananController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ImportkaryawanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\PotonganController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Change password (shared, any authenticated user)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password.form');
    Route::post('/change-password', [PasswordController::class, 'changePassword'])->name('password.change');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resource('karyawan', karyawanController::class)->except(['show']);
    Route::get('karyawan-mapping', [karyawanController::class, 'mapping'])->name('karyawan.mapping');
    Route::post('karyawan-mapping/{karyawan}', [karyawanController::class, 'updateMapping'])->name('karyawan.mapping.update');
    Route::post('karyawan/{karyawan}/reset-password', [KaryawanController::class, 'resetPassword'])->name('karyawan.reset-password');
    Route::resource('jabatan', JabatanController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('departemen', DepartemenController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->parameters(['departemen' => 'departemen']);
    Route::resource('jenis-potongan', JenisPotonganController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('input-bulanan', InputBulananController::class)->except(['show']);
    Route::post('input-bulanan-bulk', [InputBulananController::class, 'bulkStore'])->name('input-bulanan.bulk-store');

    Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
    Route::get('/import/check-period', [ImportController::class, 'checkPeriod'])->name('import.check-period');
    Route::get('/import/resume', [ImportController::class, 'resume'])->name('import.resume');
    Route::get('/import/preview', [ImportController::class, 'showPreview'])->name('import.preview');
    Route::post('/import', [ImportController::class, 'process'])->name('import.process');
    Route::post('/import/execute', [ImportController::class, 'execute'])->name('import.execute');
    Route::post('/import/collective', [ImportController::class, 'collectiveStore'])->name('import.collective');
    Route::post('/import/update-row', [ImportController::class, 'updateRow'])->name('import.update-row');
    Route::post('/import/delete-row', [ImportController::class, 'deleteRow'])->name('import.delete-row');

    Route::get('/import-karyawan', [ImportkaryawanController::class, 'showForm'])->name('import-karyawan.form');
    Route::get('/import-karyawan/template', [ImportkaryawanController::class, 'downloadTemplate'])->name('import-karyawan.template');
    Route::post('/import-karyawan', [ImportkaryawanController::class, 'process'])->name('import-karyawan.process');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportBackup'])->name('laporan.export');
    Route::post('/laporan/delete-old', [LaporanController::class, 'deleteOldData'])->name('laporan.delete-old');
});

// User routes
Route::prefix('user')->middleware(['auth', 'user'])->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
    Route::get('/potongan', [PotonganController::class, 'index'])->name('potongan.index');
    Route::get('/potongan/{inputBulanan}', [PotonganController::class, 'show'])->name('potongan.show');
});
