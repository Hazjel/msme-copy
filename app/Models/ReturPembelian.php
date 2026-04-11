<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturPembelian extends Model
{
    protected $fillable = [
        'nomor',
        'tanggal',
        'pembelian_id',
        'supplier_id',
        'user_id',
        'total',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReturPembelianDetail::class);
    }

    public static function generateNomor(): string
    {
        $prefix = 'RPB' . date('Ymd');
        $last = self::where('nomor', 'like', $prefix . '%')
            ->orderByDesc('nomor')
            ->value('nomor');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
