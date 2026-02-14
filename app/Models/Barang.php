<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'stok_barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'id_barang_satuan',
        'qty_barang',
        'stok_awal',
        'harga_barang',
        'warning_stok',
        'internal',
        'agen',
        'subagen',
        'corporate',
    ];

    protected $casts = [
        'internal' => 'boolean',
        'agen' => 'boolean',
        'subagen' => 'boolean',
        'corporate' => 'boolean',
        'harga_barang' => 'decimal:2',
    ];

    public function satuan()
    {
        return $this->belongsTo(BarangSatuan::class, 'id_barang_satuan');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_barang');
    }

    public function barangMasukDetails()
    {
        return $this->hasMany(BarangMasukDetail::class, 'id_barang');
    }

    public function barangKeluarDetails()
    {
        return $this->hasMany(BarangKeluarDetail::class, 'id_barang');
    }

    public function hargaRecords()
    {
        return $this->hasMany(BarangHarga::class, 'id_barang');
    }

    public function ketersediaan()
    {
        return $this->hasMany(BarangKetersediaan::class, 'id_barang');
    }

    /**
     * Get stock status: aman, warning, habis
     */
    public function getStatusAttribute(): string
    {
        if ($this->qty_barang <= 0) {
            return 'habis';
        }
        if ($this->qty_barang <= $this->warning_stok) {
            return 'warning';
        }
        return 'aman';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'habis' => 'red',
            'warning' => 'yellow',
            default => 'green',
        };
    }
}
