<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_barang_keluar', function (Blueprint $table) {
            // Rename id_order → order_id
            $table->renameColumn('id_order', 'order_id');

            // Rename id_divisi → department_id
            $table->renameColumn('id_divisi', 'department_id');

            // Remove id_kategori and id_agen
            $table->dropColumn(['id_kategori', 'id_agen']);

            // Add new columns
            $table->unsignedBigInteger('group_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('stok_barang_keluar', function (Blueprint $table) {
            $table->renameColumn('order_id', 'id_order');
            $table->renameColumn('department_id', 'id_divisi');
            $table->dropColumn(['group_id', 'user_id']);
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->unsignedBigInteger('id_agen')->nullable();
        });
    }
};
