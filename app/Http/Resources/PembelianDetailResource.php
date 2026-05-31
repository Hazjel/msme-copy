<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembelianDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pembelian_id' => $this->pembelian_id,
            'barang_id' => $this->barang_id,
            'barang' => $this->whenLoaded('barang', fn () => [
                'id' => $this->barang->id,
                'kode' => $this->barang->kode,
                'nama' => $this->barang->nama,
                'satuan' => $this->barang->satuan,
            ]),
            'qty' => (int) $this->qty,
            'harga' => (float) $this->harga,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}
