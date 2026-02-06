<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangHarga extends Model
{
    protected $table = 'stok_barang_harga';

    protected $fillable = [
        'id_barang',
        'id_barang_masuk',
        'id_barang_keluar',
        'qty_barang',
        'min_barang',
        'id_ref_min_barang',
        'harga_barang',
        'harga_barang_invoice',
        'tanggal_barang',
    ];

    protected $casts = [
        'tanggal_barang' => 'datetime',
        'harga_barang' => 'decimal:2',
        'harga_barang_invoice' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'id_barang_masuk');
    }

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar');
    }

    /**
     * Get available stock in this batch (for FIFO)
     */
    public function getAvailableQtyAttribute(): int
    {
        return $this->qty_barang - $this->min_barang;
    }

    /**
     * Check if this batch has available stock
     */
    public function hasAvailableStock(): bool
    {
        return $this->available_qty > 0;
    }
}
