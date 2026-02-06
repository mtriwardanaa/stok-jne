<?php

namespace App\Livewire\BarangKeluar;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BarangKeluar;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $month = '';
    public $year = '';

    protected $queryString = ['search', 'month', 'year'];

    public function mount()
    {
        $this->month = $this->month ?: now()->month;
        $this->year = $this->year ?: now()->year;
    }

    public function render()
    {
        $barangKeluars = BarangKeluar::with(['createdUser', 'details.barang', 'order'])
            ->when($this->search, fn($q) => $q->where('no_barang_keluar', 'like', "%{$this->search}%"))
            ->when($this->month, fn($q) => $q->whereMonth('tanggal', $this->month))
            ->when($this->year, fn($q) => $q->whereYear('tanggal', $this->year))
            ->latest('tanggal')
            ->paginate(15);

        return view('livewire.barang-keluar.index', compact('barangKeluars'))
            ->layout('components.layouts.app', ['title' => 'Barang Keluar']);
    }
}
