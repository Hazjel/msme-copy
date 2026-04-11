@extends('adminlte::page')

@section('title', 'Pembelian')

@section('content_header')
    <h1>Pembelian</h1>
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
            <a href="{{ route('pembelian.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Pembelian
            </a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th class="text-right">Total</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembelians as $pembelian)
                        <tr>
                            <td><strong>{{ $pembelian->nomor }}</strong></td>
                            <td>{{ $pembelian->tanggal->format('d-m-Y') }}</td>
                            <td>{{ $pembelian->supplier->nama }}</td>
                            <td class="text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('pembelian.show', $pembelian) }}" class="btn btn-info btn-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pembelian.edit', $pembelian) }}" class="btn btn-warning btn-xs">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pembelian.destroy', $pembelian) }}" method="post"
                                    class="d-inline" onsubmit="return confirm('Hapus pembelian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $pembelians->links() }}
        </div>
    </div>
@stop
