<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_invoice_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_invoice')->constrained('stok_invoice')->cascadeOnDelete();
            $table->foreignId('id_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->integer('qty');
            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_invoice_detail');
    }
};
