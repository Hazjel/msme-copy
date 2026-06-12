<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StokInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StokInputApiController extends Controller
{
    public function index(Request $request)
    {
        $query = StokInput::with(['barang', 'user'])
            ->latest('tanggal')
            ->latest('id');

        if ($barangId = $request->input('barang_id')) {
            $query->where('barang_id', $barangId);
        }
        if ($start = $request->input('start')) {
            $query->whereDate('tanggal', '>=', $start);
        }
        if ($end = $request->input('end')) {
            $query->whereDate('tanggal', '<=', $end);
        }
        if ($search = $request->input('q')) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $items = $query->paginate($perPage);

        return response()->json([
            'data' => $items->getCollection()->map(fn ($s) => $this->serialize($s)),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
                'per_page' => $items->perPage(),
            ],
        ]);
    }

    public function show(StokInput $stokInput)
    {
        $stokInput->load(['barang', 'user']);

        return response()->json(['data' => $this->serialize($stokInput)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'qty_input' => ['required', 'integer'],
            'keterangan' => ['nullable', 'string'],
            'tanggal' => ['nullable', 'date'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $record = DB::transaction(function () use ($data, $request) {
            $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);
            $qtySebelum = (int) $barang->stok;
            $qtyInput = (int) $data['qty_input'];
            $qtySetelah = $qtySebelum + $qtyInput;

            if ($qtySetelah < 0) {
                throw ValidationException::withMessages([
                    'qty_input' => "Stok tidak boleh negatif. Stok sekarang {$qtySebelum}, input {$qtyInput}, akan jadi {$qtySetelah}.",
                ]);
            }

            $barang->stok = $qtySetelah;
            $barang->save();

            return StokInput::create([
                'barang_id' => $barang->id,
                'qty_sebelum' => $qtySebelum,
                'qty_input' => $qtyInput,
                'qty_setelah' => $qtySetelah,
                'user_id' => $data['user_id'] ?? $request->user()?->id,
                'keterangan' => $data['keterangan'] ?? null,
                'tanggal' => $data['tanggal'] ?? now()->toDateString(),
            ]);
        });

        $record->load(['barang', 'user']);

        return response()->json(['data' => $this->serialize($record)], 201);
    }

    private function serialize(StokInput $s): array
    {
        return [
            'id' => $s->id,
            'barang_id' => $s->barang_id,
            'barang' => $s->barang ? [
                'id' => $s->barang->id,
                'kode' => $s->barang->kode,
                'nama' => $s->barang->nama,
                'satuan' => $s->barang->satuan,
            ] : null,
            'qty_sebelum' => $s->qty_sebelum,
            'qty_input' => $s->qty_input,
            'qty_setelah' => $s->qty_setelah,
            'user_id' => $s->user_id,
            'user_nama' => $s->user?->name,
            'keterangan' => $s->keterangan,
            'tanggal' => $s->tanggal?->format('Y-m-d'),
            'created_at' => $s->created_at?->toIso8601String(),
        ];
    }
}
