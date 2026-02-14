<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_barang_ketersediaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_barang');
            $table->string('tipe', 20); // 'internal' or 'partner'
            $table->unsignedBigInteger('partner_id')->nullable(); // NULL if tipe='internal'
            $table->timestamps();

            $table->foreign('id_barang')
                  ->references('id')
                  ->on('stok_barang')
                  ->onDelete('cascade');

            $table->unique(['id_barang', 'tipe', 'partner_id'], 'unique_ketersediaan');
        });

        // Migrate existing boolean data to pivot table
        $barangs = DB::table('stok_barang')->get();
        $now = now();

        foreach ($barangs as $barang) {
            $rows = [];

            if ($barang->internal) {
                $rows[] = [
                    'id_barang' => $barang->id,
                    'tipe' => 'internal',
                    'partner_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // agen=1 → partner_id=1 (AGEN HYBRID)
            if ($barang->agen) {
                $rows[] = [
                    'id_barang' => $barang->id,
                    'tipe' => 'partner',
                    'partner_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // subagen=1 → partner_id=2 (CABANG / SUB AGEN)
            if ($barang->subagen) {
                $rows[] = [
                    'id_barang' => $barang->id,
                    'tipe' => 'partner',
                    'partner_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // corporate=1 → partner_id=3 (CUSTOMER CORPORATE)
            if ($barang->corporate) {
                $rows[] = [
                    'id_barang' => $barang->id,
                    'tipe' => 'partner',
                    'partner_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($rows)) {
                DB::table('stok_barang_ketersediaan')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_barang_ketersediaan');
    }
};
