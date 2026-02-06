<?php

namespace App\Livewire\BarangMasuk;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\BarangHarga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $tanggal;
    public $items = [];
    public $barangList = [];
    public $supplierList = [];

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d\TH:i');
        $this->items = [['id_barang' => '', 'id_supplier' => '', 'qty_barang' => 1, 'harga_barang' => 0]];
        $this->barangList = Barang::orderBy('nama_barang')->get();
        $this->supplierList = Supplier::orderBy('nama_supplier')->get();
    }

    public function addItem()
    {
        $this->items[] = ['id_barang' => '', 'id_supplier' => '', 'qty_barang' => 1, 'harga_barang' => 0];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function save()
    {
        $this->validate([
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
                'tanggal' => $this->tanggal,
                'created_by' => $user->id,
            ]);

            foreach ($this->items as $item) {
                // Create detail
                BarangMasukDetail::create([
                    'id_barang_masuk' => $barangMasuk->id,
                    'id_barang' => $item['id_barang'],
                    'id_supplier' => $item['id_supplier'],
                    'qty_barang' => $item['qty_barang'],
                    'harga_barang' => $item['harga_barang'],
                ]);

                // Create FIFO price record
                BarangHarga::create([
                    'id_barang' => $item['id_barang'],
                    'id_barang_masuk' => $barangMasuk->id,
                    'qty_barang' => $item['qty_barang'],
                    'harga_barang' => $item['harga_barang'],
                    'tanggal_barang' => $this->tanggal,
                ]);

                // Update stock
                $barang = Barang::find($item['id_barang']);
                $barang->increment('qty_barang', $item['qty_barang']);
                $barang->update(['harga_barang' => $item['harga_barang']]);
            }

            DB::commit();
            session()->flash('success', 'Barang masuk berhasil disimpan.');
            return redirect()->route('barang-masuk.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.barang-masuk.create')
            ->layout('components.layouts.app', ['title' => 'Tambah Barang Masuk']);
    }
}
