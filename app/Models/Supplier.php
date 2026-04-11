<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['kode', 'nama', 'telepon', 'alamat'];

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }
}
