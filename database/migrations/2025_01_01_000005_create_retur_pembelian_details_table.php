<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_pembelian_id')->constrained('retur_pembelians')->cascadeOnDelete();
            $table->foreignId('pembelian_detail_id')->constrained('pembelian_details')->restrictOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->restrictOnDelete();
            $table->integer('qty');
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_pembelian_details');
    }
};
