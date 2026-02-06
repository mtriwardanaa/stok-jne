<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Order;

class Index extends Component
{
    public $month;
    public $year;
    public $reportType = 'stock';

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export
        session()->flash('info', 'Fitur export Excel akan segera tersedia.');
    }

    public function render()
    {
        $data = [];

        if ($this->reportType === 'stock') {
            $data['items'] = Barang::with('satuan')
                ->orderBy('nama_barang')
                ->get();
        } elseif ($this->reportType === 'masuk') {
            $data['items'] = BarangMasuk::with(['details.barang', 'createdUser'])
                ->whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->latest('tanggal')
                ->get();
        } elseif ($this->reportType === 'keluar') {
            $data['items'] = BarangKeluar::with(['details.barang', 'createdUser', 'order'])
                ->whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->latest('tanggal')
                ->get();
        } elseif ($this->reportType === 'order') {
            $data['items'] = Order::with(['details.barang', 'createdUser'])
                ->whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->latest('tanggal')
                ->get();
        }

        return view('livewire.report.index', $data)
            ->layout('components.layouts.app', ['title' => 'Laporan']);
    }
}
