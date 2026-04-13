<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\ReturPembelian;
use App\Models\ReturPembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturPembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturPembelian::with(['supplier', 'pembelian'])
            ->latest('tanggal')
            ->latest('id');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('pembelian', fn ($p) => $p->where('nomor', 'like', "%{$search}%"));
            });
        }

        $returs = $query->paginate(15)->withQueryString();

        return view('admin.retur_pembelian.index', compact('returs'));
    }

    public function create(Request $request)
    {
        $pembelian = null;

        if ($request->filled('pembelian_id')) {
            $pembelian = Pembelian::with(['supplier', 'details.barang'])
                ->findOrFail($request->input('pembelian_id'));
        }

        $pembelianOptions = Pembelian::with('supplier')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('admin.retur_pembelian.create', compact('pembelian', 'pembelianOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pembelian_id' => ['required', 'exists:pembelians,id'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.pembelian_detail_id' => ['required', 'exists:pembelian_details,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Minimal harus ada 1 barang dalam retur.',
        ]);

        $pembelian = Pembelian::with('details')->findOrFail($data['pembelian_id']);

        DB::transaction(function () use ($data, $pembelian, $request) {
            $retur = ReturPembelian::create([
                'nomor' => ReturPembelian::generateNomor(),
                'tanggal' => $data['tanggal'],
                'pembelian_id' => $pembelian->id,
                'supplier_id' => $pembelian->supplier_id,
                'user_id' => $request->user()?->id,
                'keterangan' => $data['keterangan'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $detail = $pembelian->details->firstWhere('id', (int) $item['pembelian_detail_id']);
                if (! $detail) {
                    throw ValidationException::withMessages([
                        'items' => 'Detail pembelian tidak valid.',
                    ]);
                }

                $qtyRetur = (int) $item['qty'];

                // Hitung total qty yang sudah pernah diretur sebelumnya untuk detail ini
                $totalSudahDiretur = ReturPembelianDetail::where('pembelian_detail_id', $detail->id)
                    ->sum('qty');

                $sisaBisaDiretur = $detail->qty - $totalSudahDiretur;

                if ($qtyRetur > $sisaBisaDiretur) {
                    throw ValidationException::withMessages([
                        'items' => "Qty retur melebihi sisa yang bisa diretur untuk barang {$detail->barang->nama}. Sisa: {$sisaBisaDiretur}.",
                    ]);
                }

                $barang = Barang::lockForUpdate()->find($detail->barang_id);
                if ($barang->stok < $qtyRetur) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$barang->nama} tidak mencukupi untuk diretur.",
                    ]);
                }

                $subtotal = $qtyRetur * $detail->harga;
                $total += $subtotal;

                $retur->details()->create([
                    'pembelian_detail_id' => $detail->id,
                    'barang_id' => $detail->barang_id,
                    'qty' => $qtyRetur,
                    'harga' => $detail->harga,
                    'subtotal' => $subtotal,
                ]);

                $barang->stok -= $qtyRetur;
                $barang->save();
            }

            $retur->update(['total' => $total]);
        });

        return redirect()
            ->route('retur-pembelian.index')
            ->with('success', 'Retur pembelian berhasil disimpan.');
    }

    public function show(ReturPembelian $returPembelian)
    {
        $returPembelian->load(['supplier', 'pembelian', 'user', 'details.barang']);

        return view('admin.retur_pembelian.show', ['retur' => $returPembelian]);
    }

    public function destroy(ReturPembelian $returPembelian)
    {
        DB::transaction(function () use ($returPembelian) {
            foreach ($returPembelian->details as $detail) {
                $barang = Barang::lockForUpdate()->find($detail->barang_id);
                if ($barang) {
                    $barang->stok += $detail->qty;
                    $barang->save();
                }
            }
            $returPembelian->delete();
        });

        return redirect()
            ->route('retur-pembelian.index')
            ->with('success', 'Retur pembelian berhasil dihapus.');
    }
}