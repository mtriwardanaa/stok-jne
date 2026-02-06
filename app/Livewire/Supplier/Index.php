<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $supplierId = null;
    public $nama_supplier = '';

    public function openCreateModal()
    {
        $this->reset(['nama_supplier', 'supplierId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['nama_supplier' => 'required|string|max:255']);

        if ($this->editMode) {
            Supplier::find($this->supplierId)->update(['nama_supplier' => $this->nama_supplier]);
            session()->flash('success', 'Supplier berhasil diupdate.');
        } else {
            Supplier::create(['nama_supplier' => $this->nama_supplier]);
            session()->flash('success', 'Supplier berhasil ditambahkan.');
        }
        $this->showModal = false;
    }

    public function delete($id)
    {
        Supplier::findOrFail($id)->delete();
        session()->flash('success', 'Supplier berhasil dihapus.');
    }

    public function render()
    {
        $suppliers = Supplier::when($this->search, fn($q) => $q->where('nama_supplier', 'like', "%{$this->search}%"))
            ->orderBy('nama_supplier')
            ->paginate(20);

        return view('livewire.supplier.index', compact('suppliers'))
            ->layout('components.layouts.app', ['title' => 'Supplier']);
    }
}
