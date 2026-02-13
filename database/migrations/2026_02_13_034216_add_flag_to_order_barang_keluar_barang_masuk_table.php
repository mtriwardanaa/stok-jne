<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stok_order', function (Blueprint $table) {
            $table->boolean('is_old')->default(false);
        });
        Schema::table('stok_barang_keluar', function (Blueprint $table) {
            $table->boolean('is_old')->default(false);
        });
        Schema::table('stok_barang_masuk', function (Blueprint $table) {
            $table->boolean('is_old')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_order', function (Blueprint $table) {
            $table->dropColumn('is_old');
        });
        Schema::table('stok_barang_keluar', function (Blueprint $table) {
            $table->dropColumn('is_old');
        });
        Schema::table('stok_barang_masuk', function (Blueprint $table) {
            $table->dropColumn('is_old');
        });
    }
};
