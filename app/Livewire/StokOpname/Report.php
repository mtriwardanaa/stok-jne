<?php

namespace App\Livewire\StokOpname;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Report extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $filterUser = '';

    protected $queryString = ['search', 'dateFrom', 'dateTo', 'filterUser'];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get SSO database name from config
        $ssoDb = config('database.connections.sso_mysql.database');
        
        $opnames = DB::table('stok_opname as o')
            ->join('stok_barang as b', 'o.id_barang', '=', 'b.id')
            ->leftJoin("{$ssoDb}.users as u", 'o.created_by', '=', 'u.id')
            ->select(
                'o.*',
                'b.kode_barang',
                'b.nama_barang',
                'u.name as user_name'
            )
            ->when($this->search, fn($q) => $q->where('b.nama_barang', 'like', "%{$this->search}%")
                ->orWhere('b.kode_barang', 'like', "%{$this->search}%")
                ->orWhere('o.no_opname', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn($q) => $q->whereDate('o.tanggal', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('o.tanggal', '<=', $this->dateTo))
            ->when($this->filterUser, fn($q) => $q->where('o.created_by', $this->filterUser))
            ->orderByDesc('o.tanggal')
            ->paginate(15);

        $users = DB::table('stok_opname as o')
            ->join("{$ssoDb}.users as u", 'o.created_by', '=', 'u.id')
            ->select('u.id', 'u.name')
            ->distinct()
            ->get();

        // Summary stats
        $stats = [
            'total' => DB::table('stok_opname')->count(),
            'this_month' => DB::table('stok_opname')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count(),
            'total_selisih_plus' => DB::table('stok_opname')
                ->where('selisih', '>', 0)
                ->sum('selisih'),
            'total_selisih_minus' => DB::table('stok_opname')
                ->where('selisih', '<', 0)
                ->sum('selisih'),
        ];

        return view('livewire.stok-opname.report', compact('opnames', 'users', 'stats'))
            ->layout('components.layouts.app', ['title' => 'Report Stock Opname']);
    }
}
