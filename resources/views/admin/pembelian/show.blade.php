@extends('adminlte::page')

@section('title', 'Detail Pembelian')

@section('content_header')
    <h1>Detail Pembelian</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $pembelian->nomor }}</h3>
            <div class="card-tools">
                <a href="{{ route('pembelian.edit', $pembelian) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('retur-pembelian.create', ['pembelian_id' => $pembelian->id]) }}"
                    class="btn btn-secondary btn-sm">
                    <i class="fas fa-undo"></i> Buat Retur
                </a>
                <a href="{{ route('pembelian.index') }}" class="btn btn-default btn-sm">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Tanggal</dt>
                <dd class="col-sm-9">{{ $pembelian->tanggal->format('d-m-Y') }}</dd>

                <dt class="col-sm-3">Supplier</dt>
                <dd class="col-sm-9">{{ $pembelian->supplier->nama }}</dd>

                <dt class="col-sm-3">Dibuat oleh</dt>
                <dd class="col-sm-9">{{ $pembelian->user?->name ?? '-' }}</dd>

                <dt class="col-sm-3">Keterangan</dt>
                <dd class="col-sm-9">{{ $pembelian->keterangan ?? '-' }}</dd>
            </dl>

            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Barang</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelian->details as $d)
                        <tr>
                            <td>{{ $d->barang->kode }} - {{ $d->barang->nama }}</td>
                            <td class="text-right">{{ $d->qty }}</td>
                            <td class="text-right">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total</th>
                        <th class="text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop
