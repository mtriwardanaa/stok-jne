<?php

namespace App\Livewire\BarangMasuk;

use Livewire\Component;
use App\Models\BarangMasuk;

class Detail extends Component
{
    public BarangMasuk $barangMasuk;

    public function mount($id)
    {
        $this->barangMasuk = BarangMasuk::with(['createdUser', 'details.barang.satuan', 'details.supplier'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.barang-masuk.detail')
            ->layout('components.layouts.app', ['title' => 'Detail Barang Masuk']);
    }
}
