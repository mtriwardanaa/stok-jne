<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Order;
use App\Models\Department;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $month;
    public $year;
    public $reportType = 'stock';
    
    // Summary Report filters
    public $summaryFilter = 'all'; // all, divisi, partner
    public $dateFrom;
    public $dateTo;
    public $selectedDivisi = '';
    public $selectedPartner = '';
    public $selectedBarang = '';
    
    // Stock Opname fields
    public $koordinatorGA = '';
    public $auditInternal = '';

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function exportExcel()
    {
        session()->flash('info', 'Fitur export Excel akan segera tersedia.');
    }
    
    public function printSummaryReport()
    {
        // Generate print view URL with parameters
        $params = http_build_query([
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'filter' => $this->summaryFilter,
            'divisi' => $this->selectedDivisi,
            'partner' => $this->selectedPartner,
            'barang' => $this->selectedBarang,
        ]);
        
        return $this->redirect(route('report.print-summary') . '?' . $params, navigate: false);
    }
    
    public function printStokOpname()
    {
        $params = http_build_query([
            'month' => $this->month,
            'year' => $this->year,
            'koordinator' => $this->koordinatorGA,
            'auditor' => $this->auditInternal,
        ]);
        
        return $this->redirect(route('report.print-opname') . '?' . $params, navigate: false);
    }
    
    public function updatedSummaryFilter()
    {
        // Reset selections when filter type changes
        $this->selectedDivisi = '';
        $this->selectedPartner = '';
    }

    public function render()
    {
        $data = [
            'departments' => Department::orderBy('name')->get(),
            'groups' => Group::orderBy('name')->get(),
            'barangList' => Barang::orderBy('nama_barang')->get(),
        ];

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
        } elseif ($this->reportType === 'summary') {
            $data['summaryData'] = $this->getSummaryData();
        } elseif ($this->reportType === 'opname') {
            $data['opnameData'] = $this->getStokOpnameData();
        }

        return view('livewire.report.index', $data)
            ->layout('components.layouts.app', ['title' => 'Laporan']);
    }
    
    private function getSummaryData()
    {
        $query = BarangKeluar::with(['details.barang', 'requestUser.department', 'requestUser.group', 'order'])
            ->whereBetween('tanggal', [$this->dateFrom, $this->dateTo]);
            
        if ($this->selectedBarang) {
            $query->whereHas('details', function($q) {
                $q->where('id_barang', $this->selectedBarang);
            });
        }
        
        $barangKeluars = $query->get();
        
        $grouped = [];
        $totalQty = 0;
        $totalNilai = 0;
        
        foreach ($barangKeluars as $bk) {
            foreach ($bk->details as $detail) {
                if ($this->selectedBarang && $detail->id_barang != $this->selectedBarang) {
                    continue;
                }
                
                // Determine organization
                $orgName = '-';
                $orgType = 'unknown';
                $orgId = null;
                
                if ($bk->requestUser) {
                    if ($bk->requestUser->department) {
                        $orgName = $bk->requestUser->department->name;
                        $orgType = 'divisi';
                        $orgId = $bk->requestUser->department->id;
                    } elseif ($bk->requestUser->group) {
                        $orgName = $bk->requestUser->group->name;
                        $orgType = 'partner';
                        $orgId = $bk->requestUser->group->id;
                    }
                } elseif ($bk->order) {
                    $orgName = $bk->order->organization_name;
                    $orgType = $bk->order->tipe === 'eksternal' ? 'partner' : 'divisi';
                }
                
                // Apply filter
                if ($this->summaryFilter === 'divisi') {
                    if ($orgType !== 'divisi') continue;
                    if ($this->selectedDivisi && $orgId != $this->selectedDivisi) continue;
                } elseif ($this->summaryFilter === 'partner') {
                    if ($orgType !== 'partner') continue;
                    if ($this->selectedPartner && $orgId != $this->selectedPartner) continue;
                }
                // 'all' filter shows everything
                
                if (!isset($grouped[$orgName])) {
                    $grouped[$orgName] = [
                        'name' => $orgName,
                        'type' => $orgType,
                        'items' => [],
                        'total_qty' => 0,
                        'total_nilai' => 0,
                    ];
                }
                
                $barangName = $detail->barang->nama_barang ?? 'Unknown';
                $harga = $detail->barang->harga_barang ?? 0;
                $nilai = $detail->qty_barang * $harga;
                
                if (!isset($grouped[$orgName]['items'][$barangName])) {
                    $grouped[$orgName]['items'][$barangName] = [
                        'nama' => $barangName,
                        'qty' => 0,
                        'nilai' => 0,
                    ];
                }
                
                $grouped[$orgName]['items'][$barangName]['qty'] += $detail->qty_barang;
                $grouped[$orgName]['items'][$barangName]['nilai'] += $nilai;
                $grouped[$orgName]['total_qty'] += $detail->qty_barang;
                $grouped[$orgName]['total_nilai'] += $nilai;
                $totalQty += $detail->qty_barang;
                $totalNilai += $nilai;
            }
        }
        
        return [
            'grouped' => $grouped,
            'total_qty' => $totalQty,
            'total_nilai' => $totalNilai,
        ];
    }
    
    private function getStokOpnameData()
    {
        $barangs = Barang::with('satuan')->orderBy('nama_barang')->get();
        
        $data = [];
        foreach ($barangs as $barang) {
            $stokMasuk = DB::table('stok_barang_masuk_detail')
                ->join('stok_barang_masuk', 'stok_barang_masuk.id', '=', 'stok_barang_masuk_detail.id_barang_masuk')
                ->where('stok_barang_masuk_detail.id_barang', $barang->id)
                ->whereMonth('stok_barang_masuk.tanggal', $this->month)
                ->whereYear('stok_barang_masuk.tanggal', $this->year)
                ->whereNull('stok_barang_masuk.deleted_at')
                ->sum('stok_barang_masuk_detail.qty_barang');
                
            $stokKeluar = DB::table('stok_barang_keluar_detail')
                ->join('stok_barang_keluar', 'stok_barang_keluar.id', '=', 'stok_barang_keluar_detail.id_barang_keluar')
                ->where('stok_barang_keluar_detail.id_barang', $barang->id)
                ->whereMonth('stok_barang_keluar.tanggal', $this->month)
                ->whereYear('stok_barang_keluar.tanggal', $this->year)
                ->whereNull('stok_barang_keluar.deleted_at')
                ->sum('stok_barang_keluar_detail.qty_barang');
            
            $stokAkhir = $barang->qty_barang;
            $stokAwal = $stokAkhir - $stokMasuk + $stokKeluar;
            
            $data[] = [
                'kode' => $barang->kode_barang,
                'nama' => $barang->nama_barang,
                'satuan' => $barang->satuan?->nama_satuan ?? '-',
                'stok_awal' => $stokAwal,
                'masuk' => $stokMasuk,
                'keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhir,
            ];
        }
        
        return $data;
    }
}
