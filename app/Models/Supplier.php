<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'stok_supplier';

    protected $fillable = [
        'nama_supplier',
    ];

    public function barangMasukDetails()
    {
        return $this->hasMany(BarangMasukDetail::class, 'id_supplier');
    }
}
