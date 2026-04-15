<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangHppLog;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with('supplier')->latest('tanggal')->latest('id');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'like', "%{$search}%"));
            });
        }

        $pembelians = $query->paginate(15)->withQueryString();

        return view('admin.pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $barangs   = Barang::orderBy('nama')->get();
        $nomor     = Pembelian::generateNomor();

        return view('admin.pembelian.create', compact('suppliers', 'barangs', 'nomor'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePembelian($request);

        DB::transaction(function () use ($data, $request) {
            $pembelian = Pembelian::create([
                'nomor'       => Pembelian::generateNomor(),
                'tanggal'     => $data['tanggal'],
                'supplier_id' => $data['supplier_id'],
                'user_id'     => $request->user()?->id,
                'keterangan'  => $data['keterangan'] ?? null,
                'total'       => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $subtotal = $item['qty'] * $item['harga'];
                $total   += $subtotal;

                $pembelian->details()->create([
                    'barang_id' => $item['barang_id'],
                    'qty'       => $item['qty'],
                    'harga'     => $item['harga'],
                    'subtotal'  => $subtotal,
                ]);

                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                $hppLama = (float) $barang->harga_pokok;
                $stokSebelum = $barang->stok;
                $hppBaru = $this->hitungHPPRataRata(
                    stokLama:  $stokSebelum,
                    hppLama:   $hppLama,
                    qtyMasuk:  $item['qty'],
                    hargaBeli: $item['harga'],
                );
                $barang->harga_pokok = $hppBaru;
                $barang->stok += $item['qty'];
                $barang->save();

                $this->logHpp(
                    tanggal: $pembelian->tanggal,
                    barangId: $barang->id,
                    qty: $item['qty'],
                    hargaBeli: (float) $item['harga'],
                    hppLama: $hppLama,
                    hppBaru: $hppBaru,
                    stokSebelum: $stokSebelum,
                    stokSetelah: $barang->stok,
                    sumberType: 'pembelian',
                    sumberId: $pembelian->id,
                    keterangan: 'Pembelian ' . $pembelian->nomor,
                );
            }

            $pembelian->update(['total' => $total]);
        });

        return redirect()
            ->route('pembelian.index')
            ->with('success', 'Pembelian berhasil disimpan.');
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'user', 'details.barang']);

        return view('admin.pembelian.show', compact('pembelian'));
    }

    public function edit(Pembelian $pembelian)
    {
        if ($pembelian->returs()->exists()) {
            return redirect()
                ->route('pembelian.show', $pembelian)
                ->with('error', 'Pembelian yang sudah pernah diretur tidak dapat diubah.');
        }

        $pembelian->load('details');
        $suppliers = Supplier::orderBy('nama')->get();
        $barangs   = Barang::orderBy('nama')->get();

        return view('admin.pembelian.edit', compact('pembelian', 'suppliers', 'barangs'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        if ($pembelian->returs()->exists()) {
            return redirect()
                ->route('pembelian.show', $pembelian)
                ->with('error', 'Pembelian yang sudah pernah diretur tidak dapat diubah.');
        }

        $data = $this->validatePembelian($request);

        DB::transaction(function () use ($data, $pembelian) {
            foreach ($pembelian->details as $detail) {
                $barang = Barang::lockForUpdate()->find($detail->barang_id);
                if ($barang) {
                    $stokTotal = $barang->stok;
                    $hppLama = (float) $barang->harga_pokok;
                    $hppSebelum = $this->reverseHPP(
                        hppWA:      $hppLama,
                        stokTotal:  $stokTotal,
                        qtyMasuk:   $detail->qty,
                        hargaBeli:  (float) $detail->harga,
                    );
                    $barang->stok       -= $detail->qty;
                    $barang->harga_pokok = max(0, $hppSebelum);
                    $barang->save();

                    $this->logHpp(
                        tanggal: $pembelian->tanggal,
                        barangId: $barang->id,
                        qty: -$detail->qty,
                        hargaBeli: (float) $detail->harga,
                        hppLama: $hppLama,
                        hppBaru: (float) $barang->harga_pokok,
                        stokSebelum: $stokTotal,
                        stokSetelah: $barang->stok,
                        sumberType: 'pembelian_update_reverse',
                        sumberId: $pembelian->id,
                        keterangan: 'Reverse update pembelian ' . $pembelian->nomor,
                    );
                }
            }

            $pembelian->details()->delete();

            $pembelian->update([
                'tanggal'     => $data['tanggal'],
                'supplier_id' => $data['supplier_id'],
                'keterangan'  => $data['keterangan'] ?? null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $subtotal = $item['qty'] * $item['harga'];
                $total   += $subtotal;

                $pembelian->details()->create([
                    'barang_id' => $item['barang_id'],
                    'qty'       => $item['qty'],
                    'harga'     => $item['harga'],
                    'subtotal'  => $subtotal,
                ]);

                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                $hppLama = (float) $barang->harga_pokok;
                $stokSebelum = $barang->stok;
                $hppBaru = $this->hitungHPPRataRata(
                    stokLama:  $stokSebelum,
                    hppLama:   $hppLama,
                    qtyMasuk:  $item['qty'],
                    hargaBeli: $item['harga'],
                );
                $barang->harga_pokok = $hppBaru;
                $barang->stok += $item['qty'];
                $barang->save();

                $this->logHpp(
                    tanggal: $pembelian->tanggal,
                    barangId: $barang->id,
                    qty: $item['qty'],
                    hargaBeli: (float) $item['harga'],
                    hppLama: $hppLama,
                    hppBaru: $hppBaru,
                    stokSebelum: $stokSebelum,
                    stokSetelah: $barang->stok,
                    sumberType: 'pembelian_update',
                    sumberId: $pembelian->id,
                    keterangan: 'Update pembelian ' . $pembelian->nomor,
                );
            }

            $pembelian->update(['total' => $total]);
        });

        return redirect()
            ->route('pembelian.show', $pembelian)
            ->with('success', 'Pembelian berhasil diperbarui.');
    }

    public function destroy(Pembelian $pembelian)
    {
        if ($pembelian->returs()->exists()) {
            return back()->with('error', 'Pembelian yang sudah pernah diretur tidak dapat dihapus.');
        }

        DB::transaction(function () use ($pembelian) {
            foreach ($pembelian->details as $detail) {
                $barang = Barang::lockForUpdate()->find($detail->barang_id);
                if ($barang) {
                    $hppLama = (float) $barang->harga_pokok;
                    $stokSebelum = $barang->stok;
                    $hppSebelum = $this->reverseHPP(
                        hppWA:      $hppLama,
                        stokTotal:  $stokSebelum,
                        qtyMasuk:   $detail->qty,
                        hargaBeli:  (float) $detail->harga,
                    );
                    $barang->stok       -= $detail->qty;
                    $barang->harga_pokok = max(0, $hppSebelum);
                    $barang->save();

                    $this->logHpp(
                        tanggal: now()->toDateString(),
                        barangId: $barang->id,
                        qty: -$detail->qty,
                        hargaBeli: (float) $detail->harga,
                        hppLama: $hppLama,
                        hppBaru: (float) $barang->harga_pokok,
                        stokSebelum: $stokSebelum,
                        stokSetelah: $barang->stok,
                        sumberType: 'pembelian_destroy',
                        sumberId: $pembelian->id,
                        keterangan: 'Hapus pembelian ' . $pembelian->nomor,
                    );
                }
            }
            $pembelian->delete();
        });

        return redirect()
            ->route('pembelian.index')
            ->with('success', 'Pembelian berhasil dihapus.');
    }

    private function logHpp(
        $tanggal,
        int $barangId,
        int $qty,
        float $hargaBeli,
        float $hppLama,
        float $hppBaru,
        int $stokSebelum,
        int $stokSetelah,
        string $sumberType,
        int $sumberId,
        ?string $keterangan = null,
    ): void {
        BarangHppLog::create([
            'tanggal' => $tanggal,
            'barang_id' => $barangId,
            'qty' => $qty,
            'harga_beli' => $hargaBeli,
            'hpp_lama' => $hppLama,
            'hpp_baru' => $hppBaru,
            'stok_sebelum' => $stokSebelum,
            'stok_setelah' => $stokSetelah,
            'sumber_type' => $sumberType,
            'sumber_id' => $sumberId,
            'keterangan' => $keterangan,
        ]);
    }

    private function hitungHPPRataRata(
        int   $stokLama,
        float $hppLama,
        int   $qtyMasuk,
        float $hargaBeli
    ): float {
        if ($stokLama <= 0) {
            return $hargaBeli;
        }

        $nilaiLama  = $stokLama * $hppLama;
        $nilaiMasuk = $qtyMasuk * $hargaBeli;
        $stokBaru   = $stokLama + $qtyMasuk;

        return ($nilaiLama + $nilaiMasuk) / $stokBaru;
    }

    private function reverseHPP(
        float $hppWA,
        int   $stokTotal,
        int   $qtyMasuk,
        float $hargaBeli
    ): float {
        $stokSebelum = $stokTotal - $qtyMasuk;

        if ($stokSebelum <= 0) {
            return 0;
        }

        $nilaiTotal = $hppWA * $stokTotal;
        $nilaiMasuk = $hargaBeli * $qtyMasuk;

        return ($nilaiTotal - $nilaiMasuk) / $stokSebelum;
    }

    private function validatePembelian(Request $request): array
    {
        return $request->validate([
            'tanggal'           => ['required', 'date'],
            'supplier_id'       => ['required', 'exists:suppliers,id'],
            'keterangan'        => ['nullable', 'string'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.qty'       => ['required', 'integer', 'min:1'],
            'items.*.harga'     => ['required', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Minimal harus ada 1 barang dalam pembelian.',
        ]);
    }
}
