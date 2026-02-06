<?php

namespace App\Livewire\BarangMasuk;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BarangMasuk;

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $barangMasuks = BarangMasuk::with(['createdUser', 'details.barang', 'details.supplier'])
            ->when($this->search, fn($q) => $q->where('no_barang_masuk', 'like', "%{$this->search}%"))
            ->when($this->month, fn($q) => $q->whereMonth('tanggal', $this->month))
            ->when($this->year, fn($q) => $q->whereYear('tanggal', $this->year))
            ->latest('tanggal')
            ->paginate(15);

        return view('livewire.barang-masuk.index', compact('barangMasuks'))
            ->layout('components.layouts.app', ['title' => 'Barang Masuk']);
    }
}
