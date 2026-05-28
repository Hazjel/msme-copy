<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\ReturPembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LaporanPembelianController extends Controller
{
    public function pembelian(Request $request)
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $start = $data['start'] ?? now()->startOfMonth()->toDateString();
        $end = $data['end'] ?? now()->toDateString();
        $supplierId = $data['supplier_id'] ?? null;

        $query = Pembelian::with(['supplier', 'details.barang'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $pembelians = $query->get();

        $totalPembelian = $pembelians->sum('total');
        $suppliers = Supplier::orderBy('nama')->get();

        return view('admin.laporan.pembelian', compact('pembelians', 'start', 'end', 'totalPembelian', 'suppliers', 'supplierId'));
    }

    public function returPembelian(Request $request)
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $start = $data['start'] ?? now()->startOfMonth()->toDateString();
        $end = $data['end'] ?? now()->toDateString();
        $supplierId = $data['supplier_id'] ?? null;

        $query = ReturPembelian::with(['supplier', 'pembelian', 'details.barang'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $returs = $query->get();

        $totalRetur = $returs->sum('total');
        $suppliers = Supplier::orderBy('nama')->get();

        return view('admin.laporan.retur_pembelian', compact('returs', 'start', 'end', 'totalRetur', 'suppliers', 'supplierId'));
    }
}
