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
        'id_user_request',
        'nama_user_request',
        'no_hp',
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

    public function requestUser()
    {
        return $this->belongsTo(User::class, 'id_user_request');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get organization name from request user or order
     */
    public function getOrganizationNameAttribute(): string
    {
        // Try from request user first
        if ($this->requestUser) {
            return $this->requestUser->organization_name;
        }
        // Then from order
        if ($this->order) {
            return $this->order->organization_name;
        }
        return '-';
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
