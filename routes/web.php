<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanPembelianController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ReturPembelianController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Pembelian (Grup 2)
    Route::resource('pembelian', PembelianController::class)
        ->except(['show'])
        ->names('pembelian');
    Route::get('pembelian/{pembelian}', [PembelianController::class, 'show'])
        ->name('pembelian.show');

    // Retur Pembelian (Grup 2)
    Route::resource('retur-pembelian', ReturPembelianController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->names('retur-pembelian');

    // Laporan (Grup 2)
    Route::get('laporan/pembelian', [LaporanPembelianController::class, 'pembelian'])
        ->name('laporan.pembelian');
    Route::get('laporan/retur-pembelian', [LaporanPembelianController::class, 'returPembelian'])
        ->name('laporan.retur-pembelian');
});
