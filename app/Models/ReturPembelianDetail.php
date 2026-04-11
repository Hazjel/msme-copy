<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturPembelianDetail extends Model
{
    protected $fillable = [
        'retur_pembelian_id',
        'pembelian_detail_id',
        'barang_id',
        'qty',
        'harga',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function returPembelian(): BelongsTo
    {
        return $this->belongsTo(ReturPembelian::class);
    }

    public function pembelianDetail(): BelongsTo
    {
        return $this->belongsTo(PembelianDetail::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
