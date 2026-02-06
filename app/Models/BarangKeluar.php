<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangKeluar extends Model
{
    use SoftDeletes;

    protected $table = 'stok_barang_keluar';

    protected $fillable = [
        'no_barang_keluar',
        'tanggal',
        'id_divisi',
        'id_kategori',
        'id_agen',
        'id_order',
        'nama_user_request',
        'distribusi_sales',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(BarangKeluarDetail::class, 'id_barang_keluar');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'id_divisi');
    }

    public function hargaRecords()
    {
        return $this->hasMany(BarangHarga::class, 'id_barang_keluar');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id_barang_keluar');
    }

    /**
     * Get total items in this stock out record
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->details->sum('qty_barang');
    }
}
