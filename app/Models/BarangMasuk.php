<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'stok_barang_masuk';

    protected $fillable = [
        'no_barang_masuk',
        'tanggal',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(BarangMasukDetail::class, 'id_barang_masuk');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function hargaRecords()
    {
        return $this->hasMany(BarangHarga::class, 'id_barang_masuk');
    }

    /**
     * Get total items in this stock in record
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->details->sum('qty_barang');
    }

    /**
     * Get total value of this stock in record
     */
    public function getTotalValueAttribute(): float
    {
        return $this->details->sum(fn($d) => $d->qty_barang * $d->harga_barang);
    }
}
