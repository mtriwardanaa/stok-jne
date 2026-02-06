<?php

namespace App\Livewire\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $month = '';
    public $year = '';

    protected $queryString = ['search', 'status', 'month', 'year'];

    public function mount()
    {
        $this->month = $this->month ?: now()->month;
        $this->year = $this->year ?: now()->year;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with(['createdUser', 'details.barang'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('no_order', 'like', "%{$this->search}%")
                          ->orWhere('nama_user_request', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->month, fn($q) => $q->whereMonth('tanggal', $this->month))
            ->when($this->year, fn($q) => $q->whereYear('tanggal', $this->year))
            ->latest('tanggal')
            ->paginate(15);

        $statusCounts = [
            'all' => Order::whereMonth('tanggal', $this->month)->whereYear('tanggal', $this->year)->count(),
            'menunggu' => Order::pending()->whereMonth('tanggal', $this->month)->whereYear('tanggal', $this->year)->count(),
            'diproses' => Order::processing()->whereMonth('tanggal', $this->month)->whereYear('tanggal', $this->year)->count(),
            'selesai' => Order::completed()->whereMonth('tanggal', $this->month)->whereYear('tanggal', $this->year)->count(),
            'ditolak' => Order::rejected()->whereMonth('tanggal', $this->month)->whereYear('tanggal', $this->year)->count(),
        ];

        return view('livewire.order.index', compact('orders', 'statusCounts'))
            ->layout('components.layouts.app', ['title' => 'Order']);
    }
}
