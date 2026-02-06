<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Order;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_barang' => Barang::count(),
            'stok_warning' => Barang::whereColumn('qty_barang', '<=', 'warning_stok')->where('qty_barang', '>', 0)->count(),
            'stok_habis' => Barang::where('qty_barang', '<=', 0)->count(),
            'order_pending' => Order::pending()->count(),
            'order_bulan_ini' => Order::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count(),
            'barang_masuk_bulan_ini' => BarangMasuk::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count(),
            'barang_keluar_bulan_ini' => BarangKeluar::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count(),
        ];

        $recentOrders = Order::with(['createdUser', 'details'])
            ->latest('tanggal')
            ->take(5)
            ->get();

        $lowStockItems = Barang::with('satuan')
            ->whereColumn('qty_barang', '<=', 'warning_stok')
            ->orderBy('qty_barang')
            ->take(10)
            ->get();

        return view('livewire.dashboard', compact('stats', 'recentOrders', 'lowStockItems'))
            ->layout('components.layouts.app', ['title' => 'Dashboard']);
    }
}
