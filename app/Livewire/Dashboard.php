<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Order;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            'total_nilai_stok' => Barang::sum(DB::raw('qty_barang * harga_barang')),
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

        // Chart data: Trend 6 bulan terakhir
        $chartTrend = $this->getMonthlyTrend();
        
        // Chart data: Order by status
        $chartOrderStatus = $this->getOrderByStatus();
        
        // Top 10 barang keluar
        $topBarangKeluar = $this->getTopBarangKeluar();
        
        // Stok per satuan
        $stokPerSatuan = $this->getStokPerSatuan();

        return view('livewire.dashboard', compact(
            'stats', 
            'recentOrders', 
            'lowStockItems',
            'chartTrend',
            'chartOrderStatus',
            'topBarangKeluar',
            'stokPerSatuan'
        ))->layout('components.layouts.app', ['title' => 'Dashboard']);
    }

    private function getMonthlyTrend(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $labels = $months->map(fn($m) => $m->format('M Y'))->toArray();
        
        $masuk = $months->map(function ($month) {
            return BarangMasuk::whereMonth('tanggal', $month->month)
                ->whereYear('tanggal', $month->year)
                ->count();
        })->toArray();

        $keluar = $months->map(function ($month) {
            return BarangKeluar::whereMonth('tanggal', $month->month)
                ->whereYear('tanggal', $month->year)
                ->count();
        })->toArray();

        $orders = $months->map(function ($month) {
            return Order::whereMonth('tanggal', $month->month)
                ->whereYear('tanggal', $month->year)
                ->count();
        })->toArray();

        return [
            'labels' => $labels,
            'masuk' => $masuk,
            'keluar' => $keluar,
            'orders' => $orders,
        ];
    }

    private function getOrderByStatus(): array
    {
        $statuses = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $labels = [
            'menunggu' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];

        $colors = [
            'menunggu' => '#f59e0b',
            'diproses' => '#3b82f6',
            'selesai' => '#10b981',
            'ditolak' => '#ef4444',
        ];

        return [
            'labels' => array_values(array_intersect_key($labels, $statuses)),
            'data' => array_values($statuses),
            'colors' => array_values(array_intersect_key($colors, $statuses)),
        ];
    }

    private function getTopBarangKeluar(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $data = DB::table('stok_barang_keluar_detail as d')
            ->join('stok_barang_keluar as k', 'k.id', '=', 'd.id_barang_keluar')
            ->join('stok_barang as b', 'b.id', '=', 'd.id_barang')
            ->whereBetween('k.tanggal', [$startOfMonth, $endOfMonth])
            ->whereNull('k.deleted_at')
            ->groupBy('d.id_barang', 'b.nama_barang')
            ->select('b.nama_barang', DB::raw('SUM(d.qty_barang) as total_qty'))
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('nama_barang')->toArray(),
            'data' => $data->pluck('total_qty')->toArray(),
        ];
    }

    private function getStokPerSatuan(): array
    {
        $data = DB::table('stok_barang as b')
            ->join('stok_barang_satuan as s', 's.id', '=', 'b.id_barang_satuan')
            ->whereNull('b.deleted_at')
            ->groupBy('s.id', 's.nama_satuan')
            ->select('s.nama_satuan', DB::raw('SUM(b.qty_barang) as total_qty'))
            ->orderByDesc('total_qty')
            ->get();

        return [
            'labels' => $data->pluck('nama_satuan')->toArray(),
            'data' => $data->pluck('total_qty')->toArray(),
        ];
    }
}
