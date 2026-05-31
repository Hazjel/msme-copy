<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturPembelianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor' => $this->nomor,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'pembelian_id' => $this->pembelian_id,
            'pembelian' => $this->whenLoaded('pembelian', fn () => [
                'id' => $this->pembelian->id,
                'nomor' => $this->pembelian->nomor,
            ]),
            'supplier_id' => $this->supplier_id,
            'supplier' => $this->whenLoaded('supplier', fn () => [
                'id' => $this->supplier->id,
                'nama' => $this->supplier->nama,
            ]),
            'total' => (float) $this->total,
            'keterangan' => $this->keterangan,
            'details' => ReturPembelianDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
