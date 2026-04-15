<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangHppLog extends Model
{
    protected $fillable = [
        'tanggal',
        'barang_id',
        'qty',
        'harga_beli',
        'hpp_lama',
        'hpp_baru',
        'stok_sebelum',
        'stok_setelah',
        'sumber_type',
        'sumber_id',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'qty' => 'integer',
            'harga_beli' => 'decimal:2',
            'hpp_lama' => 'decimal:2',
            'hpp_baru' => 'decimal:2',
            'stok_sebelum' => 'integer',
            'stok_setelah' => 'integer',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
