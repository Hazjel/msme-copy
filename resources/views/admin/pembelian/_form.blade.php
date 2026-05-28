@php
    $existingItems = (isset($pembelian) && $pembelian->exists)
        ? $pembelian->details->map(fn($d) => [
            'barang_id' => $d->barang_id,
            'qty' => $d->qty,
            'harga' => $d->harga,
        ])->toArray()
        : [];
    $oldItems = old('items', $existingItems);
    if (empty($oldItems)) {
        $oldItems = [['barang_id' => '', 'qty' => 1, 'harga' => 0]];
    }
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Nomor</label>
            <input type="text" class="form-control" value="{{ $pembelian->nomor ?? $nomor ?? '-' }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control"
                value="{{ old('tanggal', $pembelian->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Supplier <span class="text-danger">*</span></label>
            <select name="supplier_id" class="form-control" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}"
                        @selected(old('supplier_id', $pembelian->supplier_id ?? '') == $s->id)>
                        {{ $s->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Keterangan</label>
    <textarea name="keterangan" rows="2"
        class="form-control">{{ old('keterangan', $pembelian->keterangan ?? '') }}</textarea>
</div>

<h5 class="mt-3">Item Barang</h5>
<table class="table table-bordered" id="itemsTable">
    <thead class="thead-light">
        <tr>
            <th style="width: 40%;">Barang</th>
            <th style="width: 15%;">Qty</th>
            <th style="width: 20%;">Harga</th>
            <th style="width: 20%;" class="text-right">Subtotal</th>
            <th style="width: 5%;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($oldItems as $i => $row)
            <tr>
                <td>
                    <select name="items[{{ $i }}][barang_id]" class="form-control barang-select" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $b)
                            <option value="{{ $b->id }}" data-harga="{{ $b->harga_pokok }}"
                                @selected($row['barang_id'] == $b->id)>
                                {{ $b->kode }} - {{ $b->nama }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[{{ $i }}][qty]" class="form-control qty-input" min="1"
                        value="{{ $row['qty'] }}" required>
                </td>
                <td>
                    <input type="number" name="items[{{ $i }}][harga]" class="form-control harga-input"
                        min="0" step="0.01" value="{{ $row['harga'] }}" required>
                </td>
                <td class="text-right subtotal-cell align-middle">0</td>
                <td class="align-middle text-center">
                    <button type="button" class="btn btn-danger btn-xs btn-remove"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right"><strong>Total</strong></td>
            <td class="text-right"><strong id="grandTotal">0</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<button type="button" id="btnAddItem" class="btn btn-sm btn-secondary">
    <i class="fas fa-plus"></i> Tambah Baris
</button>

<div class="mt-3">
    <button type="submit" class="btn btn-primary" id="btnSubmitPembelian">
        <i class="fas fa-save"></i> Simpan
    </button>
    <a href="{{ route('pembelian.index') }}" class="btn btn-default">Batal</a>
</div>

<script>
    (function() {
            const tbody = document.querySelector('#itemsTable tbody');
            const btnAdd = document.getElementById('btnAddItem');
            const grandTotalEl = document.getElementById('grandTotal');

            function formatRupiah(n) {
                return 'Rp ' + (n || 0).toLocaleString('id-ID');
            }

            function reindex() {
                [...tbody.querySelectorAll('tr')].forEach((tr, i) => {
                    tr.querySelectorAll('[name^="items["]').forEach(el => {
                        el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
                    });
                });
            }

            function recalc() {
                let grand = 0;
                tbody.querySelectorAll('tr').forEach(tr => {
                    const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
                    const harga = parseFloat(tr.querySelector('.harga-input').value) || 0;
                    const sub = qty * harga;
                    tr.querySelector('.subtotal-cell').textContent = formatRupiah(sub);
                    grand += sub;
                });
                grandTotalEl.textContent = formatRupiah(grand);
            }

            tbody.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    const v = parseFloat(e.target.value);
                    if (isNaN(v) || v < 1) e.target.value = 1;
                }
                if (e.target.classList.contains('harga-input')) {
                    const v = parseFloat(e.target.value);
                    if (isNaN(v) || v < 0) e.target.value = 0;
                }
                recalc();
            });
            tbody.addEventListener('change', function(e) {
                if (e.target.classList.contains('barang-select')) {
                    const opt = e.target.selectedOptions[0];
                    const harga = opt?.dataset.harga;
                    if (harga) {
                        e.target.closest('tr').querySelector('.harga-input').value = harga;
                    }
                    recalc();
                }
            });
            tbody.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove')) {
                    if (tbody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                        reindex();
                        recalc();
                    }
                }
            });

            btnAdd.addEventListener('click', function() {
                const first = tbody.querySelector('tr');
                const clone = first.cloneNode(true);
                clone.querySelectorAll('input, select').forEach(el => {
                    if (el.tagName === 'SELECT') el.selectedIndex = 0;
                    else if (el.classList.contains('qty-input')) el.value = 1;
                    else if (el.classList.contains('harga-input')) el.value = 0;
                });
                tbody.appendChild(clone);
                reindex();
                recalc();
            });

        recalc();

        const form = tbody.closest('form');
        const btnSubmit = document.getElementById('btnSubmitPembelian');
        if (form && btnSubmit) {
            form.addEventListener('submit', function(e) {
                const rows = tbody.querySelectorAll('tr');
                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Minimal harus ada 1 barang.');
                    return;
                }
                for (const tr of rows) {
                    const barang = tr.querySelector('.barang-select').value;
                    const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
                    const harga = parseFloat(tr.querySelector('.harga-input').value) || 0;
                    if (!barang) {
                        e.preventDefault();
                        alert('Pilih barang untuk semua baris.');
                        return;
                    }
                    if (qty < 1) {
                        e.preventDefault();
                        alert('Qty harus minimal 1.');
                        return;
                    }
                    if (harga < 0) {
                        e.preventDefault();
                        alert('Harga tidak boleh negatif.');
                        return;
                    }
                }
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            });
        }
    })();
</script>
