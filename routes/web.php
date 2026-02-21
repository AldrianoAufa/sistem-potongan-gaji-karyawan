<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\JenisPotonganController;
use App\Http\Controllers\Admin\InputBulananController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ImportAnggotaController;
use App\Http\Controllers\Admin\LaporanController;
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

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resource('anggota', AnggotaController::class)->except(['show']);
    Route::resource('jabatan', JabatanController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('jenis-potongan', JenisPotonganController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('input-bulanan', InputBulananController::class)->except(['show', 'create']);

    Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'process'])->name('import.process');

    Route::get('/import-anggota', [ImportAnggotaController::class, 'showForm'])->name('import-anggota.form');
    Route::post('/import-anggota', [ImportAnggotaController::class, 'process'])->name('import-anggota.process');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

// User routes
Route::prefix('user')->middleware(['auth', 'user'])->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
    Route::get('/potongan', [PotonganController::class, 'index'])->name('potongan.index');
    Route::get('/potongan/{inputBulanan}', [PotonganController::class, 'show'])->name('potongan.show');
});
