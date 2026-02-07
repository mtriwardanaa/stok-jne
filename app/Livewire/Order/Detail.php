<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangHarga;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Detail extends Component
{
    public Order $order;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $rejectReason = '';
    public $approvedQty = [];
    public $orderHistory = [];

    public function mount($id)
    {
        $this->order = Order::with(['createdUser', 'approvedUser', 'rejectedUser', 'details.barang.satuan'])
            ->findOrFail($id);

        // Initialize approved quantities (limited by current stock)
        foreach ($this->order->details as $detail) {
            $currentStock = $detail->barang?->qty_barang ?? 0;
            $maxQty = min($detail->qty_barang, $currentStock);
            $this->approvedQty[$detail->id] = $detail->qty_approved ?? $maxQty;
        }
        
        // Get order history for the same organization in the same month/year
        $this->orderHistory = $this->getOrderHistory();
    }
    
    private function getOrderHistory()
    {
        $orderMonth = $this->order->tanggal->month;
        $orderYear = $this->order->tanggal->year;
        
        // Get the creator's organization info
        $creator = $this->order->createdUser;
        if (!$creator) {
            return collect([]);
        }
        
        // Get user IDs in the same organization (must query SSO database first)
        $userIdsInSameOrg = [];
        
        if ($creator->department_id) {
            // Internal user - get all users in same department
            $userIdsInSameOrg = User::where('department_id', $creator->department_id)
                ->pluck('id')
                ->toArray();
        } elseif ($creator->group_id) {
            // Partner user - get all users in same group
            $userIdsInSameOrg = User::where('group_id', $creator->group_id)
                ->pluck('id')
                ->toArray();
        } else {
            // Fallback - just this user
            $userIdsInSameOrg = [$creator->id];
        }
        
        if (empty($userIdsInSameOrg)) {
            return collect([]);
        }
        
        // Query orders created by users in the same organization
        return Order::with(['details.barang', 'createdUser'])
            ->where('id', '!=', $this->order->id)
            ->whereIn('created_by', $userIdsInSameOrg)
            ->whereMonth('tanggal', $orderMonth)
            ->whereYear('tanggal', $orderYear)
            ->orderBy('tanggal', 'asc')
            ->get();
    }

    public function openApproveModal()
    {
        $this->showApproveModal = true;
    }

    public function openRejectModal()
    {
        $this->showRejectModal = true;
    }

    public function approve()
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Create Barang Keluar
            $barangKeluar = BarangKeluar::create([
                'no_barang_keluar' => 'NBK-' . date('md') . '-' . $user->id . date('His'),
                'tanggal' => now(),
                'id_order' => $this->order->id,
                'nama_user_request' => $this->order->nama_user_request,
                'created_by' => $user->id,
            ]);

            // Process each item with FIFO
            foreach ($this->order->details as $detail) {
                $barang = Barang::find($detail->id_barang);
                $currentStock = $barang->qty_barang ?? 0;
                $qtyToDeduct = min($this->approvedQty[$detail->id] ?? 0, $currentStock);

                if ($qtyToDeduct <= 0) continue;

                // Update order detail
                $detail->update(['qty_approved' => $qtyToDeduct]);

                // Create barang keluar detail
                BarangKeluarDetail::create([
                    'id_barang_keluar' => $barangKeluar->id,
                    'id_barang' => $detail->id_barang,
                    'qty_barang' => $qtyToDeduct,
                ]);

                // FIFO: Deduct from oldest stock first
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

                // Update barang stock
                $barang = Barang::find($detail->id_barang);
                $barang->decrement('qty_barang', $qtyToDeduct);
            }

            // Update order status
            $this->order->update([
                'status' => 'selesai',
                'approved_by' => $user->id,
                'tanggal_approve' => now(),
            ]);

            DB::commit();

            $this->showApproveModal = false;
            session()->flash('success', 'Order berhasil diapprove dan barang keluar telah dibuat.');

            return redirect()->route('order.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject()
    {
        $this->validate([
            'rejectReason' => 'required|min:10',
        ], [
            'rejectReason.required' => 'Alasan penolakan harus diisi',
            'rejectReason.min' => 'Alasan penolakan minimal 10 karakter',
        ]);

        $this->order->update([
            'status' => 'ditolak',
            'rejected_by' => Auth::id(),
            'tanggal_reject' => now(),
            'rejected_text' => $this->rejectReason,
        ]);

        $this->showRejectModal = false;
        session()->flash('success', 'Order berhasil ditolak.');

        return redirect()->route('order.index');
    }

    public function render()
    {
        return view('livewire.order.detail')
            ->layout('components.layouts.app', ['title' => 'Detail Order']);
    }
}
