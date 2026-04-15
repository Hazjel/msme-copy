<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_hpp_logs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->integer('qty');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('hpp_lama', 15, 2);
            $table->decimal('hpp_baru', 15, 2);
            $table->integer('stok_sebelum');
            $table->integer('stok_setelah');
            $table->string('sumber_type', 50);
            $table->unsignedBigInteger('sumber_id');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->index(['barang_id', 'tanggal']);
            $table->index(['sumber_type', 'sumber_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_hpp_logs');
    }
};
