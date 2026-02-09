<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangHarga;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $barangKeluars = BarangKeluar::with(['createdUser', 'details.barang', 'order'])
            ->when($search, fn($q) => $q->where('no_barang_keluar', 'like', "%{$search}%"))
            ->when($month, fn($q) => $q->whereMonth('tanggal', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal', $year))
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('BarangKeluar/Index', [
            'barangKeluars' => $barangKeluars,
            'filters' => [
                'search' => $search,
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('BarangKeluar/Create', [
            'barangList' => Barang::with('satuan')->where('qty_barang', '>', 0)->orderBy('nama_barang')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_user_request' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:stok_barang,id',
            'items.*.qty_barang' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $barangKeluar = BarangKeluar::create([
                'no_barang_keluar' => 'NBK-' . date('md') . '-' . $user->id . date('His'),
                'tanggal' => $validated['tanggal'],
                'nama_user_request' => $validated['nama_user_request'],
                'created_by' => $user->id,
            ]);

            foreach ($validated['items'] as $item) {
                $barang = Barang::find($item['id_barang']);
                $qtyToDeduct = min($item['qty_barang'], $barang->qty_barang);

                BarangKeluarDetail::create([
                    'id_barang_keluar' => $barangKeluar->id,
                    'id_barang' => $item['id_barang'],
                    'qty_barang' => $qtyToDeduct,
                ]);

                // FIFO deduction
                $remainingQty = $qtyToDeduct;
                $hargaRecords = BarangHarga::where('id_barang', $item['id_barang'])
                    ->whereNull('id_barang_keluar')
                    ->whereRaw('qty_barang > min_barang')
                    ->orderBy('tanggal_barang')
                    ->get();

                foreach ($hargaRecords as $harga) {
                    if ($remainingQty <= 0) break;
                    $available = $harga->qty_barang - $harga->min_barang;
                    $toDeduct = min($available, $remainingQty);
                    $harga->update([
                        'min_barang' => $harga->min_barang + $toDeduct,
                        'id_ref_min_barang' => $barangKeluar->id,
                    ]);
                    $remainingQty -= $toDeduct;
                }

                $barang->decrement('qty_barang', $qtyToDeduct);
            }

            DB::commit();
            return redirect()->route('barang-keluar.index')->with('success', 'Barang keluar berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $barangKeluar = BarangKeluar::with(['createdUser', 'requestUser', 'details.barang.satuan', 'order'])
            ->findOrFail($id);

        return Inertia::render('BarangKeluar/Detail', [
            'barangKeluar' => $barangKeluar,
        ]);
    }
}
