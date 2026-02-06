<?php

namespace App\Livewire\BarangKeluar;

use Livewire\Component;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangHarga;
use App\Models\User;
use App\Models\Group;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $tanggal;
    public $tipe = 'internal';
    public $id_department = '';
    public $id_group = '';
    public $id_user = '';
    public $nama_user = '';
    public $no_hp = '';
    public $items = [];
    public $barangList = [];
    public $departmentList = [];
    public $groupList = [];
    public $userList = [];

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d\TH:i');
        $this->items = [['id_barang' => '', 'qty_barang' => 1, 'max_qty' => 0]];
        $this->barangList = Barang::where('qty_barang', '>', 0)->orderBy('nama_barang')->get();
        $this->departmentList = Department::orderBy('name')->get();
        $this->groupList = Group::orderBy('name')->get();
    }

    public function updatedTipe()
    {
        $this->reset(['id_department', 'id_group', 'id_user', 'nama_user', 'no_hp']);
        $this->userList = [];
    }

    public function updatedIdDepartment()
    {
        $this->reset(['id_user', 'nama_user', 'no_hp']);
        if ($this->id_department) {
            $this->userList = User::where('type', 'internal')
                ->where('department_id', $this->id_department)
                ->orderBy('name')
                ->get();
        } else {
            $this->userList = [];
        }
    }

    public function updatedIdGroup()
    {
        $this->reset(['id_user', 'nama_user', 'no_hp']);
        if ($this->id_group) {
            $this->userList = User::where('type', 'partner')
                ->where('group_id', $this->id_group)
                ->orderBy('name')
                ->get();
        } else {
            $this->userList = [];
        }
    }

    public function updatedIdUser()
    {
        if ($this->id_user) {
            $user = User::find($this->id_user);
            if ($user) {
                $this->nama_user = $user->name;
                $this->no_hp = $user->phone ?? '';
            }
        } else {
            $this->nama_user = '';
            $this->no_hp = '';
        }
    }

    public function addItem()
    {
        $this->items[] = ['id_barang' => '', 'qty_barang' => 1, 'max_qty' => 0];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updatedItems($value, $key)
    {
        if (str_ends_with($key, '.id_barang')) {
            $index = explode('.', $key)[0];
            $barang = Barang::find($value);
            $this->items[$index]['max_qty'] = $barang?->qty_barang ?? 0;
            if ($this->items[$index]['qty_barang'] > $this->items[$index]['max_qty']) {
                $this->items[$index]['qty_barang'] = $this->items[$index]['max_qty'];
            }
        }
    }

    public function save()
    {
        $this->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:internal,partner',
            'id_user' => 'required',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:stok_barang,id',
            'items.*.qty_barang' => 'required|integer|min:1',
        ], [
            'id_user.required' => 'User harus dipilih',
            'items.*.id_barang.required' => 'Barang harus dipilih',
            'items.*.qty_barang.min' => 'Qty minimal 1',
        ]);

        foreach ($this->items as $index => $item) {
            $barang = Barang::find($item['id_barang']);
            if ($item['qty_barang'] > $barang->qty_barang) {
                $this->addError("items.{$index}.qty_barang", "Qty melebihi stok ({$barang->qty_barang})");
                return;
            }
        }

        DB::beginTransaction();
        try {
            $authUser = Auth::user();

            $barangKeluar = BarangKeluar::create([
                'no_barang_keluar' => 'NBK-' . date('md') . '-' . $authUser->id . date('His'),
                'tanggal' => $this->tanggal,
                'id_user_request' => $this->id_user,
                'nama_user_request' => $this->nama_user,
                'no_hp' => $this->no_hp,
                'created_by' => $authUser->id,
            ]);

            foreach ($this->items as $item) {
                $barang = Barang::find($item['id_barang']);
                $qtyToDeduct = min($item['qty_barang'], $barang->qty_barang);

                if ($qtyToDeduct <= 0) continue;

                BarangKeluarDetail::create([
                    'id_barang_keluar' => $barangKeluar->id,
                    'id_barang' => $item['id_barang'],
                    'qty_barang' => $qtyToDeduct,
                ]);

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
            session()->flash('success', 'Barang keluar berhasil disimpan.');
            return redirect()->route('barang-keluar.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->barangList = Barang::orderBy('nama_barang')->get();
        
        return view('livewire.barang-keluar.create')
            ->layout('components.layouts.app', ['title' => 'Tambah Barang Keluar']);
    }
}
