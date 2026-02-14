<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKetersediaan;
use App\Models\BarangSatuan;
use App\Models\Partner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $filter = $request->get('filter', '');

        $barangs = Barang::with(['satuan', 'ketersediaan'])
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
        $partners = Partner::orderBy('name')->get();

        return Inertia::render('Barang/Index', [
            'barangs' => $barangs,
            'satuans' => $satuans,
            'partners' => $partners,
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
            'ketersediaan_internal' => 'boolean',
            'ketersediaan_partners' => 'array',
            'ketersediaan_partners.*' => 'integer',
        ]);

        $barang = Barang::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'id_barang_satuan' => $validated['id_barang_satuan'],
            'harga_barang' => $validated['harga_barang'],
            'warning_stok' => $validated['warning_stok'],
        ]);

        $this->syncKetersediaan($barang, $request);

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
            'ketersediaan_internal' => 'boolean',
            'ketersediaan_partners' => 'array',
            'ketersediaan_partners.*' => 'integer',
        ]);

        $barang->update([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'id_barang_satuan' => $validated['id_barang_satuan'],
            'harga_barang' => $validated['harga_barang'],
            'warning_stok' => $validated['warning_stok'],
        ]);

        $this->syncKetersediaan($barang, $request);

        return back()->with('success', 'Barang berhasil diupdate.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }

    private function syncKetersediaan(Barang $barang, Request $request): void
    {
        // Delete existing ketersediaan
        $barang->ketersediaan()->delete();

        $rows = [];

        // Internal
        if ($request->boolean('ketersediaan_internal')) {
            $rows[] = [
                'id_barang' => $barang->id,
                'tipe' => 'internal',
                'partner_id' => null,
            ];
        }

        // Partners
        $partnerIds = $request->input('ketersediaan_partners', []);
        foreach ($partnerIds as $partnerId) {
            $rows[] = [
                'id_barang' => $barang->id,
                'tipe' => 'partner',
                'partner_id' => $partnerId,
            ];
        }

        if (!empty($rows)) {
            $barang->ketersediaan()->createMany($rows);
        }
    }
}
