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
                // Determine organization from requestUser (SSO database)
                $user_request = $bk->nama_user_reques ?? '-';
                $penerima = $bk?->order?->createdUser?->name ?? $bk->requestUser?->name ?? $bk->nama_user_request ?? '-';
                
                $departmentId = $bk?->order?->createdUser?->department_id ?? $bk->requestUser?->department_id;
                $departmentName = $bk?->order?->createdUser?->department?->name ?? $bk->requestUser?->department?->name;
                $groupId = $bk?->order?->createdUser?->group_id ?? $bk->requestUser?->group_id;
                $groupName = $bk?->order?->createdUser?->group?->name ?? $bk->requestUser?->group?->name;
                
                // Fallback to createdUser if requestUser not available
                if (!$departmentId && !$groupId && $bk->createdUser) {
                    $departmentId = $bk->createdUser->department_id;
                    $departmentName = $bk->createdUser->department?->name;
                    $groupId = $bk->createdUser->group_id;
                    $groupName = $bk->createdUser->group?->name;
                }
                
                // Apply filter
                if ($this->summaryFilter === 'divisi') {
                    // Only show internal divisi (has department_id, no group_id)
                    if (!$departmentId) continue;
                    if ($this->selectedDivisi && $departmentId != $this->selectedDivisi) continue;
                } elseif ($this->summaryFilter === 'partner') {
                    // Only show partner (has group_id)
                    if (!$groupId) continue;
                    if ($this->selectedPartner && $groupId != $this->selectedPartner) continue;
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
                    'divisi' => $departmentName,
                    'group' => $groupName,
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
