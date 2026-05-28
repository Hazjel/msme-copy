@extends('adminlte::page')

@section('title', 'Tambah Retur Pembelian')

@section('content_header')
    <h1>Tambah Retur Pembelian</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! $pembelian)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilih Pembelian untuk Diretur</h3>
            </div>
            <div class="card-body">
                <form method="get">
                    <div class="form-group">
                        <label>Nomor Pembelian</label>
                        <select name="pembelian_id" class="form-control" required onchange="this.form.submit()">
                            <option value="">-- Pilih Pembelian --</option>
                            @foreach ($pembelianOptions as $opt)
                                <option value="{{ $opt->id }}">
                                    {{ $opt->nomor }} - {{ $opt->tanggal->format('d-m-Y') }} - {{ $opt->supplier->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <form action="{{ route('retur-pembelian.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pembelian_id" value="{{ $pembelian->id }}">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>No. Pembelian</label>
                                <input type="text" class="form-control" value="{{ $pembelian->nomor }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supplier</label>
                                <input type="text" class="form-control" value="{{ $pembelian->supplier->nama }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Retur <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan') }}</textarea>
                    </div>

                    <h5>Pilih Barang yang Diretur</h5>
                    <table class="table table-bordered" id="returTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;"></th>
                                <th>Barang</th>
                                <th class="text-right" style="width: 10%;">Qty Beli</th>
                                <th class="text-right" style="width: 10%;">Sudah Diretur</th>
                                <th class="text-right" style="width: 10%;">Sisa</th>
                                <th class="text-right" style="width: 13%;">Harga</th>
                                <th style="width: 15%;">Qty Retur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelian->details as $i => $detail)
                                @php $habis = $detail->sisa_retur <= 0; @endphp
                                <tr class="{{ $habis ? 'text-muted' : '' }}">
                                    <td class="text-center align-middle">
                                        <input type="hidden" name="items[{{ $i }}][pembelian_detail_id]"
                                            value="{{ $detail->id }}" disabled>
                                        <input type="checkbox" class="retur-check" {{ $habis ? 'disabled' : '' }}>
                                    </td>
                                    <td>{{ $detail->barang->kode }} - {{ $detail->barang->nama }}</td>
                                    <td class="text-right">{{ $detail->qty }}</td>
                                    <td class="text-right">{{ $detail->sudah_diretur }}</td>
                                    <td class="text-right">
                                        @if ($habis)
                                            <span class="badge badge-secondary">Habis</span>
                                        @else
                                            <span class="badge badge-success">{{ $detail->sisa_retur }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][qty]"
                                            class="form-control form-control-sm qty-input" min="1"
                                            max="{{ $detail->sisa_retur }}" value="{{ min(1, $detail->sisa_retur) }}" disabled>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> Simpan Retur
                    </button>
                    <a href="{{ route('retur-pembelian.index') }}" class="btn btn-default">Batal</a>
                </form>
            </div>
        </div>
    @endif
@stop

@section('js')
    <script>
        document.querySelectorAll('.retur-check').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                row.querySelector('.qty-input').disabled = !this.checked;
                row.querySelector('input[type="hidden"]').disabled = !this.checked;
            });
        });

        document.querySelectorAll('.qty-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const max = parseInt(this.getAttribute('max')) || 1;
                let v = parseInt(this.value);
                if (isNaN(v) || v < 1) v = 1;
                if (v > max) v = max;
                this.value = v;
            });
        });

        const form = document.querySelector('form[action*="retur-pembelian"]');
        const btnSubmit = document.getElementById('btnSubmit');
        if (form && btnSubmit) {
            form.addEventListener('submit', function(e) {
                const checked = document.querySelectorAll('.retur-check:checked').length;
                if (checked === 0) {
                    e.preventDefault();
                    alert('Pilih minimal 1 barang yang akan diretur.');
                    return;
                }
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            });
        }
    </script>
@stop
