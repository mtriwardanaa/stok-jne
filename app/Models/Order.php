<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'stok_order';

    protected $fillable = [
        'no_order',
        'tanggal',
        'id_divisi',
        'id_kategori',
        'nama_user_request',
        'hp_user_request',
        'created_by',
        'updated_by',
        'approved_by',
        'rejected_by',
        'tanggal_update',
        'tanggal_approve',
        'tanggal_reject',
        'rejected_text',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_update' => 'datetime',
        'tanggal_approve' => 'datetime',
        'tanggal_reject' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'id_order');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get organization name from the requester user
     * Internal users -> Department name
     * Partner users -> Partner/Group name
     */
    public function getOrganizationNameAttribute(): string
    {
        return $this->createdUser?->organization_name ?? $this->nama_user_request ?? '-';
    }

    public function barangKeluar()
    {
        return $this->hasOne(BarangKeluar::class, 'id_order');
    }

    /**
     * Get total items in this order
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->details->sum('qty_barang');
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'yellow',
            'diproses' => 'blue',
            'selesai' => 'green',
            'ditolak' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'menunggu');
    }

    /**
     * Scope for processing orders
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'diproses');
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Scope for rejected orders
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'ditolak');
    }
}
