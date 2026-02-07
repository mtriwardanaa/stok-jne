<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Department;
use App\Models\Group;

class Index extends Component
{
    public $dateFrom;
    public $dateTo;
    
    // Summary Report filters
    public $summaryFilter = 'all'; // all, divisi, partner
    public $selectedDivisi = '';
    public $selectedPartner = '';
    public $selectedBarang = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }
    
    public function printSummaryReport()
    {
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
            'summaryData' => $this->getSummaryData(),
        ];

        return view('livewire.report.index', $data)
            ->layout('components.layouts.app', ['title' => 'Summary Pengeluaran']);
    }
    
    private function getSummaryData()
    {
        $query = BarangKeluar::with(['details.barang.satuan', 'requestUser.department', 'requestUser.group', 'order', 'createdUser'])
            ->whereBetween('tanggal', [$this->dateFrom, $this->dateTo])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');
            
        if ($this->selectedBarang) {
            $query->whereHas('details', function($q) {
                $q->where('id_barang', $this->selectedBarang);
            });
        }
        
        $barangKeluars = $query->get();
        
        $data = [];
        $totalQty = 0;
        $totalNilai = 0;
        
        foreach ($barangKeluars as $bk) {
            foreach ($bk->details as $detail) {
                if ($this->selectedBarang && $detail->id_barang != $this->selectedBarang) {
                    continue;
                }
                
                // Determine organization
                $penerima = '-';
                $orgType = 'unknown';
                $orgId = null;
                
                if ($bk->requestUser) {
                    $penerima = $bk->requestUser->name;
                    if ($bk->requestUser->department) {
                        $orgType = 'divisi';
                        $orgId = $bk->requestUser->department->id;
                    } elseif ($bk->requestUser->group) {
                        $orgType = 'partner';
                        $orgId = $bk->requestUser->group->id;
                    }
                } elseif ($bk->order) {
                    $penerima = $bk->order->organization_name;
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
                
                $harga = $detail->barang->harga_barang ?? 0;
                $nilai = $detail->qty_barang * $harga;
                
                $data[] = [
                    'tanggal_keluar' => $bk->tanggal,
                    'no_barang_keluar' => $bk->no_barang_keluar,
                    'kode_barang' => $detail->barang->kode_barang ?? '-',
                    'nama_barang' => $detail->barang->nama_barang ?? '-',
                    'satuan' => $detail->barang->satuan->nama_satuan ?? '-',
                    'qty' => $detail->qty_barang,
                    'harga' => $harga,
                    'nilai' => $nilai,
                    'penerima' => $penerima,
                    'tanggal_request' => $bk->order?->tanggal_order ?? $bk->tanggal,
                ];
                
                $totalQty += $detail->qty_barang;
                $totalNilai += $nilai;
            }
        }
        
        return [
            'data' => $data,
            'total_qty' => $totalQty,
            'total_nilai' => $totalNilai,
        ];
    }
}
