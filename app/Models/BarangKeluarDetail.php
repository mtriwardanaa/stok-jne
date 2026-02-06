<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluarDetail extends Model
{
    protected $table = 'stok_barang_keluar_detail';

    protected $fillable = [
        'id_barang_keluar',
        'id_barang',
        'qty_barang',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
