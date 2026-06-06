<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\ReturPembelian;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $totalPembelianBulanIni = Pembelian::whereBetween('tanggal', [$startMonth, $endMonth])->sum('total');
        $jumlahPembelianBulanIni = Pembelian::whereBetween('tanggal', [$startMonth, $endMonth])->count();
        $totalPembelianAllTime = Pembelian::sum('total');
        $jumlahPembelianAllTime = Pembelian::count();

        $totalReturBulanIni = ReturPembelian::whereBetween('tanggal', [$startMonth, $endMonth])->sum('total');
        $jumlahReturBulanIni = ReturPembelian::whereBetween('tanggal', [$startMonth, $endMonth])->count();
        $totalReturAllTime = ReturPembelian::sum('total');
        $jumlahReturAllTime = ReturPembelian::count();

        $pembelianTerakhir = Pembelian::with('supplier')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $returTerakhir = ReturPembelian::with(['supplier', 'pembelian'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPembelianBulanIni',
            'jumlahPembelianBulanIni',
            'totalPembelianAllTime',
            'jumlahPembelianAllTime',
            'totalReturBulanIni',
            'jumlahReturBulanIni',
            'totalReturAllTime',
            'jumlahReturAllTime',
            'pembelianTerakhir',
            'returTerakhir'
        ));
    }
}
