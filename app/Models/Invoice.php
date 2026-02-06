<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'stok_invoice';

    protected $fillable = [
        'no_invoice',
        'id_barang_keluar',
        'tanggal_invoice',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_invoice' => 'datetime',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'unpaid' => 'red',
            default => 'gray',
        };
    }
}
