<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Department;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function printSummary(Request $request)
    {
        $dateFrom = $request->get('dateFrom', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('dateTo', now()->format('Y-m-d'));
        $filter = $request->get('filter', 'all');
        $selectedDivisi = $request->get('divisi', '');
        $selectedPartner = $request->get('partner', '');
        $selectedBarang = $request->get('barang', '');
        
        $query = BarangKeluar::with(['details.barang', 'requestUser.department', 'requestUser.group', 'order'])
            ->whereBetween('tanggal', [$dateFrom, $dateTo]);
            
        if ($selectedBarang) {
            $query->whereHas('details', function($q) use ($selectedBarang) {
                $q->where('id_barang', $selectedBarang);
            });
        }
        
        $barangKeluars = $query->get();
        
        $grouped = [];
        $totalQty = 0;
        $totalNilai = 0;
        
        foreach ($barangKeluars as $bk) {
            foreach ($bk->details as $detail) {
                if ($selectedBarang && $detail->id_barang != $selectedBarang) {
                    continue;
                }
                
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
                
                if ($filter === 'divisi') {
                    if ($orgType !== 'divisi') continue;
                    if ($selectedDivisi && $orgId != $selectedDivisi) continue;
                } elseif ($filter === 'partner') {
                    if ($orgType !== 'partner') continue;
                    if ($selectedPartner && $orgId != $selectedPartner) continue;
                }
                
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
        
        $filterLabel = match($filter) {
            'divisi' => $selectedDivisi ? Department::find($selectedDivisi)?->name : 'Semua Divisi',
            'partner' => $selectedPartner ? Group::find($selectedPartner)?->name : 'Semua Partner',
            default => 'Semua Divisi & Partner',
        };
        
        return view('pdf.report-summary', [
            'grouped' => $grouped,
            'totalQty' => $totalQty,
            'totalNilai' => $totalNilai,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'filterLabel' => $filterLabel,
        ]);
    }
    
    public function printOpname(Request $request)
    {
        $month = (int) ($request->get('month') ?: now()->month);
        $year = (int) ($request->get('year') ?: now()->year);
        $koordinator = $request->get('koordinator', '');
        $auditor = $request->get('auditor', '');
        
        $barangs = Barang::with('satuan')->orderBy('nama_barang')->get();
        
        $data = [];
        foreach ($barangs as $barang) {
            $stokMasuk = DB::table('stok_barang_masuk_detail')
                ->join('stok_barang_masuk', 'stok_barang_masuk.id', '=', 'stok_barang_masuk_detail.id_barang_masuk')
                ->where('stok_barang_masuk_detail.id_barang', $barang->id)
                ->whereMonth('stok_barang_masuk.tanggal', $month)
                ->whereYear('stok_barang_masuk.tanggal', $year)
                ->whereNull('stok_barang_masuk.deleted_at')
                ->sum('stok_barang_masuk_detail.qty_barang');
                
            $stokKeluar = DB::table('stok_barang_keluar_detail')
                ->join('stok_barang_keluar', 'stok_barang_keluar.id', '=', 'stok_barang_keluar_detail.id_barang_keluar')
                ->where('stok_barang_keluar_detail.id_barang', $barang->id)
                ->whereMonth('stok_barang_keluar.tanggal', $month)
                ->whereYear('stok_barang_keluar.tanggal', $year)
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
        
        $monthName = \Carbon\Carbon::create()->month($month)->translatedFormat('F');
        
        return view('pdf.report-opname', [
            'data' => $data,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
            'koordinator' => $koordinator,
            'auditor' => $auditor,
        ]);
    }
}
