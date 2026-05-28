@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
    <small class="text-muted">Ringkasan bulan {{ now()->translatedFormat('F Y') }}</small>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Rp {{ number_format($totalPembelianBulanIni, 0, ',', '.') }}</h3>
                    <p>Total Pembelian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                <a href="{{ route('laporan.pembelian') }}" class="small-box-footer">
                    Lihat laporan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $jumlahPembelianBulanIni }}</h3>
                    <p>Jumlah Transaksi Pembelian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('pembelian.index') }}" class="small-box-footer">
                    Lihat data <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($totalReturBulanIni, 0, ',', '.') }}</h3>
                    <p>Total Retur Pembelian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <a href="{{ route('laporan.retur-pembelian') }}" class="small-box-footer">
                    Lihat laporan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $jumlahReturBulanIni }}</h3>
                    <p>Jumlah Transaksi Retur</p>
                </div>
                <div class="icon">
                    <i class="fas fa-undo"></i>
                </div>
                <a href="{{ route('retur-pembelian.index') }}" class="small-box-footer">
                    Lihat data <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pembelian Terakhir</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Supplier</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembelianTerakhir as $p)
                                <tr>
                                    <td>{{ $p->tanggal->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('pembelian.show', $p) }}">{{ $p->nomor }}</a>
                                    </td>
                                    <td>{{ $p->supplier->nama }}</td>
                                    <td class="text-right">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada pembelian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Retur Pembelian Terakhir</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Supplier</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($returTerakhir as $r)
                                <tr>
                                    <td>{{ $r->tanggal->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('retur-pembelian.show', $r) }}">{{ $r->nomor }}</a>
                                    </td>
                                    <td>{{ $r->supplier->nama }}</td>
                                    <td class="text-right">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada retur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
