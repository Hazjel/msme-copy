@extends('adminlte::page')

@section('title', 'Laporan Retur Pembelian')

@section('content_header')
    <h1>Laporan Retur Pembelian</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="get" class="form-inline">
                <div class="form-group mr-2">
                    <label class="mr-2">Dari</label>
                    <input type="date" name="start" value="{{ $start }}" class="form-control form-control-sm">
                </div>
                <div class="form-group mr-2">
                    <label class="mr-2">Sampai</label>
                    <input type="date" name="end" value="{{ $end }}" class="form-control form-control-sm">
                </div>
                <button class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </form>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Retur</th>
                        <th>No. Pembelian</th>
                        <th>Supplier</th>
                        <th>Barang</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returs as $retur)
                        @foreach ($retur->details as $idx => $detail)
                            <tr>
                                @if ($idx === 0)
                                    <td rowspan="{{ $retur->details->count() }}">
                                        {{ $retur->tanggal->format('d-m-Y') }}
                                    </td>
                                    <td rowspan="{{ $retur->details->count() }}">{{ $retur->nomor }}</td>
                                    <td rowspan="{{ $retur->details->count() }}">{{ $retur->pembelian->nomor }}</td>
                                    <td rowspan="{{ $retur->details->count() }}">{{ $retur->supplier->nama }}</td>
                                @endif
                                <td>{{ $detail->barang->nama }}</td>
                                <td class="text-right">{{ $detail->qty }}</td>
                                <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada data retur pembelian pada
                                rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-right">Total Retur</th>
                        <th class="text-right">Rp {{ number_format($totalRetur, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop
