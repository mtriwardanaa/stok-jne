<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->string('no_opname')->unique();
            $table->dateTime('tanggal');
            $table->foreignId('id_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih');
            $table->enum('tipe_adjustment', ['masuk', 'keluar', 'none']);
            $table->text('alasan');
            $table->string('foto_bukti')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('id_barang_masuk')->nullable();
            $table->unsignedBigInteger('id_barang_keluar')->nullable();
            $table->timestamps();

            $table->foreign('id_barang_masuk')->references('id')->on('stok_barang_masuk')->nullOnDelete();
            $table->foreign('id_barang_keluar')->references('id')->on('stok_barang_keluar')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};
