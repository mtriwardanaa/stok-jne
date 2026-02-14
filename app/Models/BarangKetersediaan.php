<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKetersediaan extends Model
{
    protected $table = 'stok_barang_ketersediaan';

    protected $fillable = [
        'id_barang',
        'tipe',
        'partner_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
