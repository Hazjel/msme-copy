<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PembelianResource;
use App\Models\Barang;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianApiController extends Controller
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

        if ($start = $request->input('start')) {
            $query->whereDate('tanggal', '>=', $start);
        }
        if ($end = $request->input('end')) {
            $query->whereDate('tanggal', '<=', $end);
        }
        if ($supplierId = $request->input('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        $perPage = (int) $request->input('per_page', 15);
        $pembelians = $query->paginate($perPage);

        return PembelianResource::collection($pembelians);
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'details.barang']);

        return new PembelianResource($pembelian);
    }

    public function store(Request $request)
    {
        $data = $this->validatePembelian($request);

        $pembelian = DB::transaction(function () use ($data, $request) {
            $pembelian = Pembelian::create([
                'nomor' => Pembelian::generateNomor(),
                'tanggal' => $data['tanggal'],
                'supplier_id' => $data['supplier_id'],
                'user_id' => $data['user_id'] ?? $request->user()?->id,
                'keterangan' => $data['keterangan'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['qty'] * $item['harga'];
                $total += $subtotal;

                $pembelian->details()->create([
                    'barang_id' => $item['barang_id'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ]);

                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                $barang->stok += $item['qty'];
                $barang->harga_pokok = $item['harga'];
                $barang->save();
            }

            $pembelian->update(['total' => $total]);

            return $pembelian;
        });

        $pembelian->load(['supplier', 'details.barang']);

        return (new PembelianResource($pembelian))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        if ($pembelian->returs()->exists()) {
            return response()->json([
                'message' => 'Pembelian yang sudah pernah diretur tidak dapat diubah.',
            ], 422);
        }

        $data = $this->validatePembelian($request);

        DB::transaction(function () use ($data, $pembelian) {
            foreach ($pembelian->details as $detail) {
                $barang = Barang::lockForUpdate()->find($detail->barang_id);
                $barang->stok -= $detail->qty;
                $barang->save();
            }
            $pembelian->details()->delete();

            $pembelian->update([
                'tanggal' => $data['tanggal'],
                'supplier_id' => $data['supplier_id'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['qty'] * $item['harga'];
                $total += $subtotal;

                $pembelian->details()->create([
                    'barang_id' => $item['barang_id'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ]);

                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                $barang->stok += $item['qty'];
                $barang->harga_pokok = $item['harga'];
                $barang->save();
            }

            $pembelian->update(['total' => $total]);
        });

        $pembelian->load(['supplier', 'details.barang']);

        return new PembelianResource($pembelian);
    }

    public function destroy(Pembelian $pembelian)
    {
        if ($pembelian->returs()->exists()) {
            return response()->json([
                'message' => 'Pembelian yang sudah pernah diretur tidak dapat dihapus.',
            ], 422);
        }

        DB::transaction(function () use ($pembelian) {
            foreach ($pembelian->details as $detail) {
                $barang = Barang::lockForUpdate()->find($detail->barang_id);
                if ($barang) {
                    $barang->stok -= $detail->qty;
                    $barang->save();
                }
            }
            $pembelian->delete();
        });

        return response()->json(['message' => 'Pembelian berhasil dihapus.']);
    }

    private function validatePembelian(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
