<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangHarga;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $orders = Order::with(['createdUser', 'details.barang'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('no_order', 'like', "%{$search}%")
                          ->orWhere('nama_user_request', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($month, fn($q) => $q->whereMonth('tanggal', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal', $year))
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        $statusCounts = [
            'all' => Order::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->count(),
            'menunggu' => Order::pending()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->count(),
            'diproses' => Order::processing()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->count(),
            'selesai' => Order::completed()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->count(),
            'ditolak' => Order::rejected()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->count(),
        ];

        return Inertia::render('Order/Index', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['createdUser', 'approvedUser', 'rejectedUser', 'details.barang.satuan'])
            ->findOrFail($id);

        // Initialize approved quantities
        $approvedQty = [];
        foreach ($order->details as $detail) {
            $currentStock = $detail->barang?->qty_barang ?? 0;
            $maxQty = min($detail->qty_barang, $currentStock);
            $approvedQty[$detail->id] = $detail->qty_approved ?? $maxQty;
        }

        // Get order history
        $orderHistory = $this->getOrderHistory($order);

        return Inertia::render('Order/Detail', [
            'order' => $order,
            'approvedQty' => $approvedQty,
            'orderHistory' => $orderHistory,
        ]);
    }

    private function getOrderHistory(Order $order)
    {
        $orderMonth = $order->tanggal->month;
        $orderYear = $order->tanggal->year;
        $creator = $order->createdUser;

        if (!$creator) return [];

        $userIdsInSameOrg = [];
        if ($creator->department_id) {
            $userIdsInSameOrg = User::where('department_id', $creator->department_id)->pluck('id')->toArray();
        } elseif ($creator->group_id) {
            $userIdsInSameOrg = User::where('group_id', $creator->group_id)->pluck('id')->toArray();
        } else {
            $userIdsInSameOrg = [$creator->id];
        }

        if (empty($userIdsInSameOrg)) return [];

        return Order::with(['details.barang', 'createdUser'])
            ->where('id', '!=', $order->id)
            ->whereIn('created_by', $userIdsInSameOrg)
            ->whereMonth('tanggal', $orderMonth)
            ->whereYear('tanggal', $orderYear)
            ->orderBy('tanggal', 'asc')
            ->get();
    }

    public function approve(Request $request, Order $order)
    {
        $approvedQty = $request->input('approvedQty', []);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Validate stock availability for each detail
            foreach ($order->details as $detail) {
                $qtyToApprove = $approvedQty[$detail->id] ?? 0;
                if ($qtyToApprove <= 0) continue;

                $masuk = DB::table('stok_barang_masuk_detail')
                    ->where('id_barang', $detail->id_barang)
                    ->sum('qty_barang');
                $keluar = DB::table('stok_barang_keluar_detail')
                    ->where('id_barang', $detail->id_barang)
                    ->sum('qty_barang');
                $currentStock = $masuk - $keluar;

                if ($qtyToApprove > $currentStock) {
                    $namaBarang = $detail->barang->nama_barang ?? 'Unknown';
                    DB::rollback();
                    return back()->with('error', "Qty approve untuk \"{$namaBarang}\" ({$qtyToApprove}) melebihi stok tersedia ({$currentStock}).");
                }
            }

            $barangKeluar = BarangKeluar::create([
                'no_barang_keluar' => 'NBK-' . date('md') . '-' . $user->id . date('His'),
                'tanggal' => now(),
                'id_order' => $order->id,
                'nama_user_request' => $order->nama_user_request,
                'created_by' => $user->id,
            ]);

            foreach ($order->details as $detail) {
                $barang = Barang::find($detail->id_barang);
                $currentStock = $barang->qty_barang ?? 0;
                $qtyToDeduct = min($approvedQty[$detail->id] ?? 0, $currentStock);

                if ($qtyToDeduct <= 0) continue;

                $detail->update(['qty_approved' => $qtyToDeduct]);

                BarangKeluarDetail::create([
                    'id_barang_keluar' => $barangKeluar->id,
                    'id_barang' => $detail->id_barang,
                    'qty_barang' => $qtyToDeduct,
                ]);

                // FIFO deduction
                $remainingQty = $qtyToDeduct;
                $hargaRecords = BarangHarga::where('id_barang', $detail->id_barang)
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

            $order->update([
                'status' => 'selesai',
                'approved_by' => $user->id,
                'tanggal_approve' => now(),
            ]);

            DB::commit();
            return redirect()->route('order.index')->with('success', 'Order berhasil diapprove.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rejectReason' => 'required|min:10',
        ]);

        $order->update([
            'status' => 'ditolak',
            'rejected_by' => Auth::id(),
            'tanggal_reject' => now(),
            'rejected_text' => $validated['rejectReason'],
        ]);

        return redirect()->route('order.index')->with('success', 'Order berhasil ditolak.');
    }
}
