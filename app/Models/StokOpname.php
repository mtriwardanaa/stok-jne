<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $table = 'stok_opname';

    protected $fillable = [
        'no_opname',
        'tanggal',
        'id_barang',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'tipe_adjustment',
        'alasan',
        'foto_bukti',
        'created_by',
        'id_barang_masuk',
        'id_barang_keluar',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'id_barang_masuk');
    }

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar');
    }

    public function getTipeColorAttribute(): string
    {
        return match ($this->tipe_adjustment) {
            'masuk' => 'green',
            'keluar' => 'red',
            default => 'gray',
        };
    }

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe_adjustment) {
            'masuk' => 'Stok Ditambah',
            'keluar' => 'Stok Dikurangi',
            default => 'Tidak Ada Perubahan',
        };
    }
}
