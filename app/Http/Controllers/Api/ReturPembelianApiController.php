<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReturPembelianResource;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\ReturPembelian;
use App\Models\ReturPembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturPembelianApiController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturPembelian::with(['supplier', 'pembelian'])
            ->latest('tanggal')
            ->latest('id');

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
        $returs = $query->paginate($perPage);

        return ReturPembelianResource::collection($returs);
    }

    public function show(ReturPembelian $returPembelian)
    {
        $returPembelian->load(['supplier', 'pembelian', 'details.barang']);

        return new ReturPembelianResource($returPembelian);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pembelian_id' => ['required', 'exists:pembelians,id'],
            'tanggal' => ['required', 'date'],
            'user_id' => ['nullable', 'exists:users,id'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.pembelian_detail_id' => ['required', 'exists:pembelian_details,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $pembelian = Pembelian::with('details')->findOrFail($data['pembelian_id']);

        $retur = DB::transaction(function () use ($data, $pembelian, $request) {
            $retur = ReturPembelian::create([
                'nomor' => ReturPembelian::generateNomor(),
                'tanggal' => $data['tanggal'],
                'pembelian_id' => $pembelian->id,
                'supplier_id' => $pembelian->supplier_id,
                'user_id' => $data['user_id'] ?? $request->user()?->id,
                'keterangan' => $data['keterangan'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $detail = $pembelian->details->firstWhere('id', (int) $item['pembelian_detail_id']);
                if (! $detail) {
                    throw ValidationException::withMessages(['items' => 'Detail pembelian tidak valid.']);
                }

                $qtyRetur = (int) $item['qty'];

                $totalSudahDiretur = ReturPembelianDetail::where('pembelian_detail_id', $detail->id)->sum('qty');
                $sisaBisaDiretur = $detail->qty - $totalSudahDiretur;

                if ($qtyRetur > $sisaBisaDiretur) {
                    throw ValidationException::withMessages([
                        'items' => "Qty retur melebihi sisa untuk barang {$detail->barang->nama}. Sisa: {$sisaBisaDiretur}.",
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

            return $retur;
        });

        $retur->load(['supplier', 'pembelian', 'details.barang']);

        return (new ReturPembelianResource($retur))
            ->response()
            ->setStatusCode(201);
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

        return response()->json(['message' => 'Retur pembelian berhasil dihapus.']);
    }

    public function sisaRetur(Pembelian $pembelian)
    {
        $pembelian->load('details.barang');

        $detailIds = $pembelian->details->pluck('id')->toArray();
        $sudahDireturMap = ReturPembelianDetail::whereIn('pembelian_detail_id', $detailIds)
            ->selectRaw('pembelian_detail_id, SUM(qty) as total_retur')
            ->groupBy('pembelian_detail_id')
            ->pluck('total_retur', 'pembelian_detail_id')
            ->toArray();

        $items = $pembelian->details->map(function ($detail) use ($sudahDireturMap) {
            $sudahRetur = (int) ($sudahDireturMap[$detail->id] ?? 0);

            return [
                'pembelian_detail_id' => $detail->id,
                'barang_id' => $detail->barang_id,
                'barang_nama' => $detail->barang->nama,
                'qty_beli' => (int) $detail->qty,
                'sudah_diretur' => $sudahRetur,
                'sisa_retur' => max(0, $detail->qty - $sudahRetur),
                'harga' => (float) $detail->harga,
            ];
        });

        return response()->json([
            'pembelian_id' => $pembelian->id,
            'nomor' => $pembelian->nomor,
            'items' => $items,
        ]);
    }
}
