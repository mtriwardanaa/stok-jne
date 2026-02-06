<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Barang;
use App\Models\BarangSatuan;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = '';
    public $showModal = false;
    public $editMode = false;
    public $barangId = null;

    public $kode_barang = '';
    public $nama_barang = '';
    public $id_barang_satuan = '';
    public $harga_barang = 0;
    public $warning_stok = 10;
    public $internal = false;
    public $agen = false;
    public $subagen = false;
    public $corporate = false;

    protected $queryString = ['search', 'filter'];

    protected $rules = [
        'kode_barang' => 'required|string|max:50',
        'nama_barang' => 'required|string|max:255',
        'id_barang_satuan' => 'required|exists:stok_barang_satuan,id',
        'harga_barang' => 'required|numeric|min:0',
        'warning_stok' => 'required|integer|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['kode_barang', 'nama_barang', 'id_barang_satuan', 'harga_barang', 'warning_stok', 'internal', 'agen', 'subagen', 'corporate', 'barangId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $this->barangId = $barang->id;
        $this->kode_barang = $barang->kode_barang;
        $this->nama_barang = $barang->nama_barang;
        $this->id_barang_satuan = $barang->id_barang_satuan;
        $this->harga_barang = $barang->harga_barang;
        $this->warning_stok = $barang->warning_stok;
        $this->internal = $barang->internal;
        $this->agen = $barang->agen;
        $this->subagen = $barang->subagen;
        $this->corporate = $barang->corporate;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'id_barang_satuan' => $this->id_barang_satuan,
            'harga_barang' => $this->harga_barang,
            'warning_stok' => $this->warning_stok,
            'internal' => $this->internal,
            'agen' => $this->agen,
            'subagen' => $this->subagen,
            'corporate' => $this->corporate,
        ];

        if ($this->editMode) {
            Barang::find($this->barangId)->update($data);
            session()->flash('success', 'Barang berhasil diupdate.');
        } else {
            Barang::create($data);
            session()->flash('success', 'Barang berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        Barang::findOrFail($id)->delete();
        session()->flash('success', 'Barang berhasil dihapus.');
    }

    public function render()
    {
        $barangs = Barang::with('satuan')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('kode_barang', 'like', "%{$this->search}%")
                          ->orWhere('nama_barang', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter === 'warning', fn($q) => $q->whereColumn('qty_barang', '<=', 'warning_stok')->where('qty_barang', '>', 0))
            ->when($this->filter === 'habis', fn($q) => $q->where('qty_barang', '<=', 0))
            ->when($this->filter === 'aman', fn($q) => $q->whereColumn('qty_barang', '>', 'warning_stok'))
            ->orderBy('nama_barang')
            ->paginate(20);

        $satuans = BarangSatuan::orderBy('nama_satuan')->get();

        return view('livewire.barang.index', compact('barangs', 'satuans'))
            ->layout('components.layouts.app', ['title' => 'Master Barang']);
    }
}
