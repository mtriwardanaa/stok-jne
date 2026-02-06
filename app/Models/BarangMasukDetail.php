<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasukDetail extends Model
{
    protected $table = 'stok_barang_masuk_detail';

    protected $fillable = [
        'id_barang_masuk',
        'id_barang',
        'id_supplier',
        'qty_barang',
        'harga_barang',
    ];

    protected $casts = [
        'harga_barang' => 'decimal:2',
    ];

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'id_barang_masuk');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    /**
     * Get subtotal for this detail
     */
    public function getSubtotalAttribute(): float
    {
        return $this->qty_barang * $this->harga_barang;
    }
}
