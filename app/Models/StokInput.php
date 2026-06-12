<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokInput extends Model
{
    protected $table = 'stok_inputs';

    protected $fillable = [
        'barang_id',
        'qty_sebelum',
        'qty_input',
        'qty_setelah',
        'user_id',
        'keterangan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'qty_sebelum' => 'integer',
            'qty_input' => 'integer',
            'qty_setelah' => 'integer',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
