<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangHarga;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $barangMasuks = BarangMasuk::with(['createdUser', 'details.barang', 'details.supplier'])
            ->when($search, fn($q) => $q->where('no_barang_masuk', 'like', "%{$search}%"))
            ->when($month, fn($q) => $q->whereMonth('tanggal', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal', $year))
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('BarangMasuk/Index', [
            'barangMasuks' => $barangMasuks,
            'filters' => [
                'search' => $search,
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('BarangMasuk/Create', [
            'barangList' => Barang::orderBy('nama_barang')->get(),
            'supplierList' => Supplier::orderBy('nama_supplier')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:stok_barang,id',
            'items.*.id_supplier' => 'required|exists:stok_supplier,id',
            'items.*.qty_barang' => 'required|integer|min:1',
            'items.*.harga_barang' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $barangMasuk = BarangMasuk::create([
                'no_barang_masuk' => 'NBM-' . date('md') . '-' . $user->id . date('His'),
                'tanggal' => $validated['tanggal'],
                'created_by' => $user->id,
            ]);

            foreach ($validated['items'] as $item) {
                BarangMasukDetail::create([
                    'id_barang_masuk' => $barangMasuk->id,
                    'id_barang' => $item['id_barang'],
                    'id_supplier' => $item['id_supplier'],
                    'qty_barang' => $item['qty_barang'],
                    'harga_barang' => $item['harga_barang'],
                ]);

                BarangHarga::create([
                    'id_barang' => $item['id_barang'],
                    'id_barang_masuk' => $barangMasuk->id,
                    'qty_barang' => $item['qty_barang'],
                    'harga_barang' => $item['harga_barang'],
                    'tanggal_barang' => $validated['tanggal'],
                ]);

                $barang = Barang::find($item['id_barang']);
                $barang->increment('qty_barang', $item['qty_barang']);
                $barang->update(['harga_barang' => $item['harga_barang']]);
            }

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['createdUser', 'details.barang.satuan', 'details.supplier'])
            ->findOrFail($id);

        return Inertia::render('BarangMasuk/Detail', [
            'barangMasuk' => $barangMasuk,
        ]);
    }
}
