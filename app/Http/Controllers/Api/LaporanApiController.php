<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PembelianResource;
use App\Http\Resources\ReturPembelianResource;
use App\Models\Pembelian;
use App\Models\ReturPembelian;
use Illuminate\Http\Request;

class LaporanApiController extends Controller
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

        $query = Pembelian::with(['supplier', 'details.barang'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id');

        if (! empty($data['supplier_id'])) {
            $query->where('supplier_id', $data['supplier_id']);
        }

        $pembelians = $query->get();

        return response()->json([
            'start' => $start,
            'end' => $end,
            'supplier_id' => $data['supplier_id'] ?? null,
            'total' => (float) $pembelians->sum('total'),
            'jumlah_transaksi' => $pembelians->count(),
            'data' => PembelianResource::collection($pembelians),
        ]);
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

        $query = ReturPembelian::with(['supplier', 'pembelian', 'details.barang'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id');

        if (! empty($data['supplier_id'])) {
            $query->where('supplier_id', $data['supplier_id']);
        }

        $returs = $query->get();

        return response()->json([
            'start' => $start,
            'end' => $end,
            'supplier_id' => $data['supplier_id'] ?? null,
            'total' => (float) $returs->sum('total'),
            'jumlah_transaksi' => $returs->count(),
            'data' => ReturPembelianResource::collection($returs),
        ]);
    }

    public function dashboard(Request $request)
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return response()->json([
            'periode' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'bulan_ini' => [
                'total_pembelian' => (float) Pembelian::whereBetween('tanggal', [$start, $end])->sum('total'),
                'jumlah_pembelian' => Pembelian::whereBetween('tanggal', [$start, $end])->count(),
                'total_retur' => (float) ReturPembelian::whereBetween('tanggal', [$start, $end])->sum('total'),
                'jumlah_retur' => ReturPembelian::whereBetween('tanggal', [$start, $end])->count(),
            ],
            'all_time' => [
                'total_pembelian' => (float) Pembelian::sum('total'),
                'jumlah_pembelian' => Pembelian::count(),
                'total_retur' => (float) ReturPembelian::sum('total'),
                'jumlah_retur' => ReturPembelian::count(),
            ],
        ]);
    }
}
