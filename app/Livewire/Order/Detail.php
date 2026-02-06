<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangHarga;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Detail extends Component
{
    public Order $order;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $rejectReason = '';
    public $approvedQty = [];
    public $distribusiSales = '';

    public function mount($id)
    {
        $this->order = Order::with(['createdUser', 'approvedUser', 'rejectedUser', 'details.barang.satuan', 'department'])
            ->findOrFail($id);

        // Initialize approved quantities
        foreach ($this->order->details as $detail) {
            $this->approvedQty[$detail->id] = $detail->qty_approved ?? $detail->qty_barang;
        }
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
                'id_divisi' => $this->order->id_divisi,
                'id_kategori' => $this->order->id_kategori,
                'id_order' => $this->order->id,
                'nama_user_request' => $this->order->nama_user_request,
                'distribusi_sales' => $this->distribusiSales,
                'created_by' => $user->id,
            ]);

            // Process each item with FIFO
            foreach ($this->order->details as $detail) {
                $qtyToDeduct = $this->approvedQty[$detail->id] ?? $detail->qty_barang;

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
