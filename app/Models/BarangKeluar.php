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
        'department_id',
        'group_id',
        'user_id',
        'order_id',
        'nama_user_request',
        'distribusi_sales',
        'created_by',
        'updated_by',
        'is_old',
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
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function requestUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
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
     * Get organization name from department or group
     */
    public function getOrganizationNameAttribute(): string
    {
        if ($this->department) {
            return $this->department->name;
        }
        if ($this->group) {
            return $this->group->name;
        }
        // Fallback to request user
        if ($this->requestUser) {
            return $this->requestUser->organization_name;
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
