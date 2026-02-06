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
        // Supplier
        Schema::create('stok_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier');
            $table->timestamps();
            $table->softDeletes();
        });

        // Satuan Barang (Unit)
        Schema::create('stok_barang_satuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan');
            $table->timestamps();
        });

        // Master Barang
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('id_barang_satuan')->constrained('stok_barang_satuan')->cascadeOnDelete();
            $table->integer('qty_barang')->default(0);
            $table->integer('stok_awal')->default(0);
            $table->decimal('harga_barang', 15, 2)->default(0);
            $table->integer('warning_stok')->default(10);
            $table->boolean('internal')->default(false);
            $table->boolean('agen')->default(false);
            $table->boolean('subagen')->default(false);
            $table->boolean('corporate')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Barang Masuk (Stock In)
        Schema::create('stok_barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_barang_masuk')->unique();
            $table->dateTime('tanggal');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stok_barang_masuk_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang_masuk')->constrained('stok_barang_masuk')->cascadeOnDelete();
            $table->foreignId('id_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->foreignId('id_supplier')->constrained('stok_supplier')->cascadeOnDelete();
            $table->integer('qty_barang');
            $table->decimal('harga_barang', 15, 2);
            $table->timestamps();
        });

        // Barang Keluar (Stock Out)
        Schema::create('stok_barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('no_barang_keluar')->unique();
            $table->dateTime('tanggal');
            $table->unsignedBigInteger('id_divisi')->nullable();
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->unsignedBigInteger('id_agen')->nullable();
            $table->unsignedBigInteger('id_order')->nullable();
            $table->string('nama_user_request')->nullable();
            $table->string('distribusi_sales')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stok_barang_keluar_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang_keluar')->constrained('stok_barang_keluar')->cascadeOnDelete();
            $table->foreignId('id_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->integer('qty_barang');
            $table->timestamps();
        });

        // FIFO Stock Price Tracking
        Schema::create('stok_barang_harga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->unsignedBigInteger('id_barang_masuk')->nullable();
            $table->unsignedBigInteger('id_barang_keluar')->nullable();
            $table->integer('qty_barang');
            $table->integer('min_barang')->default(0); // Used quantity from this batch
            $table->unsignedBigInteger('id_ref_min_barang')->nullable(); // Reference to keluar record
            $table->decimal('harga_barang', 15, 2);
            $table->decimal('harga_barang_invoice', 15, 2)->nullable();
            $table->dateTime('tanggal_barang');
            $table->timestamps();

            $table->foreign('id_barang_masuk')->references('id')->on('stok_barang_masuk')->nullOnDelete();
            $table->foreign('id_barang_keluar')->references('id')->on('stok_barang_keluar')->nullOnDelete();
        });

        // Order (Request from apps-jne)
        Schema::create('stok_order', function (Blueprint $table) {
            $table->id();
            $table->string('no_order')->unique();
            $table->dateTime('tanggal');
            $table->unsignedBigInteger('id_divisi')->nullable();
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('nama_user_request')->nullable();
            $table->string('hp_user_request')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->dateTime('tanggal_update')->nullable();
            $table->dateTime('tanggal_approve')->nullable();
            $table->dateTime('tanggal_reject')->nullable();
            $table->text('rejected_text')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stok_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order')->constrained('stok_order')->cascadeOnDelete();
            $table->foreignId('id_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->integer('qty_barang');
            $table->integer('qty_approved')->nullable();
            $table->timestamps();
        });

        // Invoice
        Schema::create('stok_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->foreignId('id_barang_keluar')->constrained('stok_barang_keluar')->cascadeOnDelete();
            $table->dateTime('tanggal_invoice');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_invoice');
        Schema::dropIfExists('stok_order_detail');
        Schema::dropIfExists('stok_order');
        Schema::dropIfExists('stok_barang_harga');
        Schema::dropIfExists('stok_barang_keluar_detail');
        Schema::dropIfExists('stok_barang_keluar');
        Schema::dropIfExists('stok_barang_masuk_detail');
        Schema::dropIfExists('stok_barang_masuk');
        Schema::dropIfExists('stok_barang');
        Schema::dropIfExists('stok_barang_satuan');
        Schema::dropIfExists('stok_supplier');
    }
};
