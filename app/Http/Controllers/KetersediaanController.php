<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKetersediaan;
use App\Models\Partner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KetersediaanController extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->get('tipe', 'internal'); // 'internal' or partner_id
        $search = $request->get('search', '');

        $partners = Partner::orderBy('name')->get();

        // Get all barang with ketersediaan
        $barangs = Barang::with('satuan')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('kode_barang', 'like', "%{$search}%")
                          ->orWhere('nama_barang', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_barang')
            ->get();

        // Get currently checked barang IDs for this tipe
        $query = BarangKetersediaan::query();
        if ($tipe === 'internal') {
            $query->where('tipe', 'internal');
        } else {
            $query->where('tipe', 'partner')->where('partner_id', $tipe);
        }
        $checkedIds = $query->pluck('id_barang')->toArray();

        return Inertia::render('Ketersediaan/Index', [
            'barangs' => $barangs,
            'partners' => $partners,
            'checkedIds' => $checkedIds,
            'filters' => [
                'tipe' => $tipe,
                'search' => $search,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required|string',
            'checked_ids' => 'array',
            'checked_ids.*' => 'integer|exists:stok_barang,id',
        ]);

        $tipe = $validated['tipe'];
        $checkedIds = $validated['checked_ids'] ?? [];

        // Delete existing ketersediaan for this tipe
        $deleteQuery = BarangKetersediaan::query();
        if ($tipe === 'internal') {
            $deleteQuery->where('tipe', 'internal');
        } else {
            $deleteQuery->where('tipe', 'partner')->where('partner_id', $tipe);
        }
        $deleteQuery->delete();

        // Insert new ketersediaan
        $rows = [];
        $now = now();
        foreach ($checkedIds as $barangId) {
            $rows[] = [
                'id_barang' => $barangId,
                'tipe' => $tipe === 'internal' ? 'internal' : 'partner',
                'partner_id' => $tipe === 'internal' ? null : $tipe,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            BarangKetersediaan::insert($rows);
        }

        $label = $tipe === 'internal' ? 'Internal' : Partner::find($tipe)?->name ?? 'Partner';

        return back()->with('success', "Ketersediaan untuk \"{$label}\" berhasil disimpan.");
    }
}
