<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangHarga;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\Department;
use App\Models\Group;
use App\Models\User;
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

        $barangKeluars = BarangKeluar::with(['createdUser', 'requestUser', 'details.barang', 'order'])
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
            'barangList' => Barang::with(['satuan', 'ketersediaan'])->where('qty_barang', '>', 0)->orderBy('nama_barang')->get(),
            'departments' => Department::orderBy('name')->get(),
            'groups' => Group::with('partner')->orderBy('name')->get(),
            'users' => User::select('id', 'name', 'department_id', 'group_id', 'type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_user_request' => 'nullable|string|max:255',
            'user_id' => ['required', \Illuminate\Validation\Rule::exists('sso_mysql.users', 'id')],
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:stok_barang,id',
            'items.*.qty_barang' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Validate stock availability for each item
            foreach ($validated['items'] as $item) {
                $masuk = DB::table('stok_barang_masuk_detail')
                    ->where('id_barang', $item['id_barang'])
                    ->sum('qty_barang');
                $keluar = DB::table('stok_barang_keluar_detail')
                    ->where('id_barang', $item['id_barang'])
                    ->sum('qty_barang');
                $currentStock = $masuk - $keluar;

                if ($item['qty_barang'] > $currentStock) {
                    $barang = Barang::find($item['id_barang']);
                    DB::rollback();
                    return back()->with('error', "Qty untuk \"{$barang->nama_barang}\" ({$item['qty_barang']}) melebihi stok tersedia ({$currentStock}).");
                }
            }

            // Get nama_user_request and org from selected user or manual input
            $namaUserRequest = $validated['nama_user_request'] ?? '';
            $selectedUserId = $validated['user_id'] ?? null;
            $departmentId = null;
            $groupId = null;

            if ($selectedUserId) {
                $selectedUser = User::find($selectedUserId);
                if ($selectedUser) {
                    $namaUserRequest = $selectedUser->name;
                    $departmentId = $selectedUser->department_id;
                    $groupId = $selectedUser->group_id;
                }
            }

            $barangKeluar = BarangKeluar::create([
                'no_barang_keluar' => 'NBK-' . date('md') . '-' . $user->id . date('His'),
                'tanggal' => $validated['tanggal'],
                'user_id' => $selectedUserId,
                'department_id' => $departmentId,
                'group_id' => $groupId,
                'nama_user_request' => $namaUserRequest,
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
        $barangKeluar = BarangKeluar::with(['createdUser', 'requestUser', 'department', 'group', 'details.barang.satuan', 'order', 'invoice.details.barang.satuan'])
            ->findOrFail($id);

        return Inertia::render('BarangKeluar/Detail', [
            'barangKeluar' => $barangKeluar,
        ]);
    }
}
