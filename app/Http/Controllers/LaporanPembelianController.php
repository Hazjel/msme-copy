<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\ReturPembelian;
use Illuminate\Http\Request;

class LaporanPembelianController extends Controller
{
    public function pembelian(Request $request)
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        $start = $data['start'] ?? now()->startOfMonth()->toDateString();
        $end = $data['end'] ?? now()->toDateString();

        $pembelians = Pembelian::with(['supplier', 'details.barang'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $totalPembelian = $pembelians->sum('total');

        return view('admin.laporan.pembelian', compact('pembelians', 'start', 'end', 'totalPembelian'));
    }

    public function returPembelian(Request $request)
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        $start = $data['start'] ?? now()->startOfMonth()->toDateString();
        $end = $data['end'] ?? now()->toDateString();

        $returs = ReturPembelian::with(['supplier', 'pembelian', 'details.barang'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $totalRetur = $returs->sum('total');

        return view('admin.laporan.retur_pembelian', compact('returs', 'start', 'end', 'totalRetur'));
    }
}
