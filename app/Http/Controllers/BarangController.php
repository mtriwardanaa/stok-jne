<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangSatuan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $filter = $request->get('filter', '');

        $barangs = Barang::with('satuan')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('kode_barang', 'like', "%{$search}%")
                          ->orWhere('nama_barang', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'warning', fn($q) => $q->whereColumn('qty_barang', '<=', 'warning_stok')->where('qty_barang', '>', 0))
            ->when($filter === 'habis', fn($q) => $q->where('qty_barang', '<=', 0))
            ->when($filter === 'aman', fn($q) => $q->whereColumn('qty_barang', '>', 'warning_stok'))
            ->orderBy('nama_barang')
            ->paginate(20)
            ->withQueryString();

        $satuans = BarangSatuan::orderBy('nama_satuan')->get();

        return Inertia::render('Barang/Index', [
            'barangs' => $barangs,
            'satuans' => $satuans,
            'filters' => [
                'search' => $search,
                'filter' => $filter,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:255',
            'id_barang_satuan' => 'required|exists:stok_barang_satuan,id',
            'harga_barang' => 'required|numeric|min:0',
            'warning_stok' => 'required|integer|min:0',
            'internal' => 'boolean',
            'agen' => 'boolean',
            'subagen' => 'boolean',
            'corporate' => 'boolean',
        ]);

        Barang::create($validated);

        return back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:255',
            'id_barang_satuan' => 'required|exists:stok_barang_satuan,id',
            'harga_barang' => 'required|numeric|min:0',
            'warning_stok' => 'required|integer|min:0',
            'internal' => 'boolean',
            'agen' => 'boolean',
            'subagen' => 'boolean',
            'corporate' => 'boolean',
        ]);

        $barang->update($validated);

        return back()->with('success', 'Barang berhasil diupdate.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
