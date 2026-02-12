<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StokOpnameController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $search = $request->get('search', '');

        $barangs = DB::table('stok_barang')
            ->when($search, fn($q) => $q->where('nama_barang', 'like', "%{$search}%")
                ->orWhere('kode_barang', 'like', "%{$search}%"))
            ->orderBy('nama_barang')
            ->get();

        $barangsWithStock = $barangs->map(function ($barang) {
            $masuk = DB::table('stok_barang_masuk_detail')
                ->where('id_barang', $barang->id)
                ->sum('qty_barang');
            $keluar = DB::table('stok_barang_keluar_detail')
                ->where('id_barang', $barang->id)
                ->sum('qty_barang');

            $barang->stok_sistem = $masuk - $keluar;
            $barang->has_opname_this_month = DB::table('stok_opname')
                ->where('id_barang', $barang->id)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->exists();

            return $barang;
        });

        $allBarangs = DB::table('stok_barang')->orderBy('nama_barang')->get();

        $opnameHistory = StokOpname::with(['barang', 'createdUser'])
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'desc')
            ->get();

        return Inertia::render('StokOpname/Index', [
            'barangsWithStock' => $barangsWithStock,
            'allBarangs' => $allBarangs,
            'opnameHistory' => $opnameHistory,
            'filters' => [
                'month' => $month,
                'year' => $year,
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:stok_barang,id',
            'stok_fisik' => 'required|integer|min:0',
            'alasan' => 'required|min:10',
            'foto_bukti' => 'nullable|image|max:2048',
        ]);

        // Check frequency limit
        $hasOpnameThisMonth = DB::table('stok_opname')
            ->where('id_barang', $validated['id_barang'])
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->exists();

        if ($hasOpnameThisMonth) {
            return back()->with('error', 'Barang ini sudah di-opname bulan ini!');
        }

        // Calculate stock sistem
        $masuk = DB::table('stok_barang_masuk_detail')
            ->where('id_barang', $validated['id_barang'])
            ->sum('qty_barang');
        $keluar = DB::table('stok_barang_keluar_detail')
            ->where('id_barang', $validated['id_barang'])
            ->sum('qty_barang');
        $stokSistem = $masuk - $keluar;
        $selisih = $validated['stok_fisik'] - $stokSistem;

        if ($selisih == 0) {
            return back()->with('error', 'Stok fisik sama dengan stok sistem, tidak perlu adjustment.');
        }

        DB::beginTransaction();
        try {
            $noOpname = 'OPN-' . date('Ymd-His');
            $fotoPath = null;

            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->store('opname-bukti', 'public');
            }

            $tipeAdjustment = 'none';
            $idBarangMasuk = null;
            $idBarangKeluar = null;

            if ($selisih > 0) {
                $tipeAdjustment = 'masuk';
                $idBarangMasuk = $this->createBarangMasukAdjustment($validated['id_barang'], $noOpname, abs($selisih));
            } elseif ($selisih < 0) {
                $tipeAdjustment = 'keluar';
                $idBarangKeluar = $this->createBarangKeluarAdjustment($validated['id_barang'], $noOpname, abs($selisih));
            }

            DB::table('stok_opname')->insert([
                'no_opname' => $noOpname,
                'tanggal' => now(),
                'id_barang' => $validated['id_barang'],
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $validated['stok_fisik'],
                'selisih' => $selisih,
                'tipe_adjustment' => $tipeAdjustment,
                'alasan' => $validated['alasan'],
                'foto_bukti' => $fotoPath,
                'created_by' => Auth::id(),
                'id_barang_masuk' => $idBarangMasuk,
                'id_barang_keluar' => $idBarangKeluar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Stock Opname berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required|min:10',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:stok_barang,id',
            'items.*.stok_fisik' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $noOpnameBatch = 'OPN-' . date('Ymd-His');
            $savedCount = 0;
            $skippedCount = 0;
            $itemIndex = 0;

            foreach ($validated['items'] as $item) {
                // Skip if already opnamed this month
                $hasOpnameThisMonth = DB::table('stok_opname')
                    ->where('id_barang', $item['id_barang'])
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->exists();

                if ($hasOpnameThisMonth) {
                    $skippedCount++;
                    continue;
                }

                // Calculate stock sistem
                $masuk = DB::table('stok_barang_masuk_detail')
                    ->where('id_barang', $item['id_barang'])
                    ->sum('qty_barang');
                $keluar = DB::table('stok_barang_keluar_detail')
                    ->where('id_barang', $item['id_barang'])
                    ->sum('qty_barang');
                $stokSistem = $masuk - $keluar;
                $selisih = $item['stok_fisik'] - $stokSistem;

                // Skip if no difference
                if ($selisih == 0) {
                    continue;
                }

                $itemIndex++;
                $noOpname = $noOpnameBatch . '-' . str_pad($itemIndex, 3, '0', STR_PAD_LEFT);

                $tipeAdjustment = 'none';
                $idBarangMasuk = null;
                $idBarangKeluar = null;

                if ($selisih > 0) {
                    $tipeAdjustment = 'masuk';
                    $idBarangMasuk = $this->createBarangMasukAdjustment($item['id_barang'], $noOpname, abs($selisih));
                } elseif ($selisih < 0) {
                    $tipeAdjustment = 'keluar';
                    $idBarangKeluar = $this->createBarangKeluarAdjustment($item['id_barang'], $noOpname, abs($selisih));
                }

                DB::table('stok_opname')->insert([
                    'no_opname' => $noOpname,
                    'tanggal' => now(),
                    'id_barang' => $item['id_barang'],
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $item['stok_fisik'],
                    'selisih' => $selisih,
                    'tipe_adjustment' => $tipeAdjustment,
                    'alasan' => $validated['alasan'],
                    'foto_bukti' => null,
                    'created_by' => Auth::id(),
                    'id_barang_masuk' => $idBarangMasuk,
                    'id_barang_keluar' => $idBarangKeluar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $savedCount++;
            }

            if ($savedCount === 0) {
                DB::rollBack();
                $msg = $skippedCount > 0
                    ? 'Semua barang sudah di-opname bulan ini atau tidak ada selisih.'
                    : 'Tidak ada item dengan selisih stok untuk disimpan.';
                return back()->with('error', $msg);
            }

            DB::commit();
            $msg = "Stock Opname berhasil! {$savedCount} item tersimpan.";
            if ($skippedCount > 0) {
                $msg .= " {$skippedCount} item dilewati (sudah di-opname bulan ini).";
            }
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function createBarangMasukAdjustment($idBarang, $noOpname, $qty): int
    {
        $barangMasukId = DB::table('stok_barang_masuk')->insertGetId([
            'no_barang_masuk' => 'ADJ-IN-' . $noOpname,
            'tanggal' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stok_barang_masuk_detail')->insert([
            'id_barang_masuk' => $barangMasukId,
            'id_barang' => $idBarang,
            'id_supplier' => 1,
            'qty_barang' => $qty,
            'harga_barang' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $barangMasukId;
    }

    private function createBarangKeluarAdjustment($idBarang, $noOpname, $qty): int
    {
        $barangKeluarId = DB::table('stok_barang_keluar')->insertGetId([
            'no_barang_keluar' => 'ADJ-OUT-' . $noOpname,
            'tanggal' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stok_barang_keluar_detail')->insert([
            'id_barang_keluar' => $barangKeluarId,
            'id_barang' => $idBarang,
            'qty_barang' => $qty,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $barangKeluarId;
    }

    public function report(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $barangs = DB::table('stok_barang')
            ->leftJoin('stok_barang_satuan', 'stok_barang.id_barang_satuan', '=', 'stok_barang_satuan.id')
            ->select('stok_barang.*', 'stok_barang_satuan.nama_satuan')
            ->orderBy('stok_barang.nama_barang')
            ->get();

        $opnameData = $barangs->map(function ($barang) use ($month, $year) {
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

            $totalMasuk = DB::table('stok_barang_masuk_detail')
                ->where('id_barang', $barang->id)
                ->sum('qty_barang');
            $totalKeluar = DB::table('stok_barang_keluar_detail')
                ->where('id_barang', $barang->id)
                ->sum('qty_barang');

            $stokAkhir = $totalMasuk - $totalKeluar;
            $stokAwal = $stokAkhir - $stokMasuk + $stokKeluar;

            return [
                'kode' => $barang->kode_barang,
                'nama' => $barang->nama_barang,
                'satuan' => $barang->nama_satuan ?? '-',
                'stok_awal' => $stokAwal,
                'masuk' => $stokMasuk,
                'keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhir,
            ];
        });

        return Inertia::render('StokOpname/Report', [
            'opnameData' => $opnameData,
            'filters' => [
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }
}
