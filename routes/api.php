<?php

use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\Api\MasterDataApiController;
use App\Http\Controllers\Api\PembelianApiController;
use App\Http\Controllers\Api\ReturPembelianApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    // Master data (read-only — milik Kelompok 5)
    Route::get('suppliers', [MasterDataApiController::class, 'suppliers'])->name('suppliers');
    Route::get('barangs', [MasterDataApiController::class, 'barangs'])->name('barangs');

    // Pembelian (Kelompok 2)
    Route::apiResource('pembelian', PembelianApiController::class);

    // Retur Pembelian (Kelompok 2)
    Route::get('pembelian/{pembelian}/sisa-retur', [ReturPembelianApiController::class, 'sisaRetur'])
        ->name('pembelian.sisa-retur');
    Route::apiResource('retur-pembelian', ReturPembelianApiController::class)
        ->only(['index', 'show', 'store', 'destroy']);

    // Laporan (Kelompok 2)
    Route::get('dashboard', [LaporanApiController::class, 'dashboard'])->name('dashboard');
    Route::get('laporan/pembelian', [LaporanApiController::class, 'pembelian'])->name('laporan.pembelian');
    Route::get('laporan/retur-pembelian', [LaporanApiController::class, 'returPembelian'])->name('laporan.retur-pembelian');
});
