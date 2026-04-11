<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'harga_pokok',
        'harga_jual',
        'stok',
    ];

    protected function casts(): array
    {
        return [
            'harga_pokok' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'stok' => 'integer',
        ];
    }
}
