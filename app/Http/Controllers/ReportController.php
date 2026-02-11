<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Department;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('dateFrom', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('dateTo', now()->format('Y-m-d'));
        $summaryFilter = $request->get('summaryFilter', 'all');
        $selectedDivisi = $request->get('selectedDivisi', '');
        $selectedPartner = $request->get('selectedPartner', '');
        $selectedBarang = $request->get('selectedBarang', '');

        return Inertia::render('Report/Index', [
            'filters' => [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'summaryFilter' => $summaryFilter,
                'selectedDivisi' => $selectedDivisi,
                'selectedPartner' => $selectedPartner,
                'selectedBarang' => $selectedBarang,
            ],
            'departments' => Department::orderBy('name')->get()->map(fn($d) => [
                'value' => $d->id,
                'label' => $d->name,
            ]),
            'groups' => Group::orderBy('name')->get()->map(fn($g) => [
                'value' => $g->id,
                'label' => $g->name,
            ]),
            'barangList' => Barang::orderBy('nama_barang')->get()->map(fn($b) => [
                'value' => $b->id,
                'label' => $b->nama_barang,
            ]),
            'summaryData' => $this->getSummaryData($dateFrom, $dateTo, $summaryFilter, $selectedDivisi, $selectedPartner, $selectedBarang),
        ]);
    }
    
    private function getSummaryData($dateFrom, $dateTo, $summaryFilter, $selectedDivisi, $selectedPartner, $selectedBarang)
    {
        $query = BarangKeluar::with(['details.barang.satuan', 'requestUser.department', 'requestUser.group', 'order.createdUser', 'createdUser'])
            ->whereBetween('tanggal', [$dateFrom, $dateTo])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');
            
        if ($selectedBarang) {
            $query->whereHas('details', function($q) use ($selectedBarang) {
                $q->where('id_barang', $selectedBarang);
            });
        }
        
        $barangKeluars = $query->get();
        
        $data = [];
        $totalQty = 0;
        $totalNilai = 0;
        
        foreach ($barangKeluars as $bk) {
            foreach ($bk->details as $detail) {
                if ($selectedBarang && $detail->id_barang != $selectedBarang) {
                    continue;
                }
                
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
                if ($summaryFilter === 'divisi') {
                    if (!$departmentId) continue;
                    if ($selectedDivisi && $departmentId != $selectedDivisi) continue;
                } elseif ($summaryFilter === 'partner') {
                    if (!$groupId) continue;
                    if ($selectedPartner && $groupId != $selectedPartner) continue;
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
    
    public function printSummary(Request $request)
    {
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $filter = $request->get('filter', 'all');
        $divisi = $request->get('divisi');
        $partner = $request->get('partner');
        $barang = $request->get('barang');
        
        $summaryData = $this->getSummaryData($dateFrom, $dateTo, $filter, $divisi, $partner, $barang);
        
        $data = $summaryData['data'];
        $totalQty = $summaryData['total_qty'];
        $totalNilai = $summaryData['total_nilai'];
        
        $department = $divisi ? Department::find($divisi) : null;
        $group = $partner ? Group::find($partner) : null;
        $barangItem = $barang ? Barang::find($barang) : null;
        
        // Build filter label
        $filterParts = [];
        if ($department) {
            $filterParts[] = 'Divisi: ' . $department->name;
        }
        if ($group) {
            $filterParts[] = 'Partner: ' . $group->name;
        }
        if ($barangItem) {
            $filterParts[] = 'Barang: ' . $barangItem->nama_barang;
        }
        $filterLabel = !empty($filterParts) ? implode(' | ', $filterParts) : 'Semua Data';
        
        return view('pdf.report-summary', compact('data', 'totalQty', 'totalNilai', 'dateFrom', 'dateTo', 'filterLabel'));
    }
    
    public function printOpname(Request $request)
    {
        // Existing print opname logic can be added here
        return view('pdf.report-opname');
    }
}
