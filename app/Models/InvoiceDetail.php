<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'stok_invoice_detail';

    protected $fillable = [
        'id_invoice',
        'id_barang',
        'qty',
        'harga',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_invoice');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
