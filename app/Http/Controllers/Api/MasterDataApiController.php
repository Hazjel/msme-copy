<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Http\Request;

class MasterDataApiController extends Controller
{
    public function suppliers(Request $request)
    {
        $query = Supplier::orderBy('nama');

        if ($search = $request->input('q')) {
            $query->where('nama', 'like', "%{$search}%");
        }

        return response()->json([
            'data' => $query->get()->map(fn ($s) => [
                'id' => $s->id,
                'nama' => $s->nama,
            ]),
        ]);
    }

    public function barangs(Request $request)
    {
        $query = Barang::orderBy('nama');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->get()->map(fn ($b) => [
                'id' => $b->id,
                'kode' => $b->kode,
                'nama' => $b->nama,
                'satuan' => $b->satuan,
                'harga_pokok' => (float) $b->harga_pokok,
                'harga_jual' => (float) $b->harga_jual,
                'stok' => (int) $b->stok,
            ]),
        ]);
    }
}
