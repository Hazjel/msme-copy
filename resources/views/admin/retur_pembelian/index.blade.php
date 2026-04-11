@extends('adminlte::page')

@section('title', 'Retur Pembelian')

@section('content_header')
    <h1>Retur Pembelian</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <form method="get" class="form-inline flex-grow-1">
                <div class="input-group input-group-sm" style="max-width: 320px;">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Cari nomor / supplier...">
                    <div class="input-group-append">
                        <button class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
            <a href="{{ route('retur-pembelian.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Retur
            </a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>No. Pembelian</th>
                        <th>Supplier</th>
                        <th class="text-right">Total</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returs as $retur)
                        <tr>
                            <td><strong>{{ $retur->nomor }}</strong></td>
                            <td>{{ $retur->tanggal->format('d-m-Y') }}</td>
                            <td>{{ $retur->pembelian->nomor }}</td>
                            <td>{{ $retur->supplier->nama }}</td>
                            <td class="text-right">Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('retur-pembelian.show', $retur) }}" class="btn btn-info btn-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('retur-pembelian.destroy', $retur) }}" method="post"
                                    class="d-inline" onsubmit="return confirm('Hapus retur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Belum ada data retur pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $returs->links() }}
        </div>
    </div>
@stop
