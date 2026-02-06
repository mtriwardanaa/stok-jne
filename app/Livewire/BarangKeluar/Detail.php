<?php

namespace App\Livewire\BarangKeluar;

use Livewire\Component;
use App\Models\BarangKeluar;

class Detail extends Component
{
    public BarangKeluar $barangKeluar;

    public function mount($id)
    {
        $this->barangKeluar = BarangKeluar::with(['createdUser', 'details.barang.satuan', 'order'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.barang-keluar.detail')
            ->layout('components.layouts.app', ['title' => 'Detail Barang Keluar']);
    }
}
