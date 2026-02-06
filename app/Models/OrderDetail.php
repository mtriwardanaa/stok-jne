<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'stok_order_detail';

    protected $fillable = [
        'id_order',
        'id_barang',
        'qty_barang',
        'qty_approved',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
