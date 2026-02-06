<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangSatuan extends Model
{
    protected $table = 'stok_barang_satuan';

    protected $fillable = [
        'nama_satuan',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_barang_satuan');
    }
}
