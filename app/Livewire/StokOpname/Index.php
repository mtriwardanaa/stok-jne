<?php

namespace App\Livewire\StokOpname;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    
    // Form fields
    public $selectedBarang = null;
    public $stokSistem = 0;
    public $stokFisik = 0;
    public $selisih = 0;
    public $alasan = '';
    public $fotoBukti;
    
    // Frequency limit check
    public $hasOpnameThisMonth = false;
    public $lastOpnameDate = null;

    protected $rules = [
        'selectedBarang' => 'required',
        'stokFisik' => 'required|integer|min:0',
        'alasan' => 'required|min:10',
        'fotoBukti' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'selectedBarang.required' => 'Pilih barang terlebih dahulu',
        'stokFisik.required' => 'Stok fisik wajib diisi',
        'stokFisik.min' => 'Stok fisik tidak boleh negatif',
        'alasan.required' => 'Alasan wajib diisi',
        'alasan.min' => 'Alasan minimal 10 karakter',
        'fotoBukti.image' => 'File harus berupa gambar',
        'fotoBukti.max' => 'Ukuran file maksimal 2MB',
    ];

    public function updatedSelectedBarang($value)
    {
        if ($value) {
            $this->calculateStokSistem();
            $this->checkFrequencyLimit();
        }
    }

    public function updatedStokFisik()
    {
        $this->selisih = $this->stokFisik - $this->stokSistem;
    }

    private function calculateStokSistem()
    {
        // Calculate stock from barang masuk - barang keluar
        $masuk = DB::table('stok_barang_masuk_detail')
            ->where('id_barang', $this->selectedBarang)
            ->sum('qty_barang');

        $keluar = DB::table('stok_barang_keluar_detail')
            ->where('id_barang', $this->selectedBarang)
            ->sum('qty_barang');

        $this->stokSistem = $masuk - $keluar;
        $this->selisih = $this->stokFisik - $this->stokSistem;
    }

    private function checkFrequencyLimit()
    {
        // Check if this barang already has opname this month
        $existingOpname = DB::table('stok_opname')
            ->where('id_barang', $this->selectedBarang)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->first();

        $this->hasOpnameThisMonth = !is_null($existingOpname);
        $this->lastOpnameDate = $existingOpname?->tanggal;
    }

    public function openModal()
    {
        $this->reset(['selectedBarang', 'stokSistem', 'stokFisik', 'selisih', 'alasan', 'fotoBukti', 'hasOpnameThisMonth', 'lastOpnameDate']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        if ($this->hasOpnameThisMonth) {
            session()->flash('error', 'Barang ini sudah di-opname bulan ini!');
            return;
        }

        if ($this->selisih == 0) {
            session()->flash('error', 'Stok fisik sama dengan stok sistem, tidak perlu adjustment.');
            return;
        }

        DB::beginTransaction();
        try {
            $noOpname = 'OPN-' . date('Ymd-His');
            $fotoPath = null;

            // Upload foto if exists
            if ($this->fotoBukti) {
                $fotoPath = $this->fotoBukti->store('opname-bukti', 'public');
            }

            // Determine adjustment type
            $tipeAdjustment = 'none';
            $idBarangMasuk = null;
            $idBarangKeluar = null;

            if ($this->selisih > 0) {
                // Stok fisik > sistem -> need to add (barang masuk)
                $tipeAdjustment = 'masuk';
                $idBarangMasuk = $this->createBarangMasukAdjustment($noOpname, abs($this->selisih));
            } elseif ($this->selisih < 0) {
                // Stok fisik < sistem -> need to reduce (barang keluar)
                $tipeAdjustment = 'keluar';
                $idBarangKeluar = $this->createBarangKeluarAdjustment($noOpname, abs($this->selisih));
            }

            // Create opname record
            DB::table('stok_opname')->insert([
                'no_opname' => $noOpname,
                'tanggal' => now(),
                'id_barang' => $this->selectedBarang,
                'stok_sistem' => $this->stokSistem,
                'stok_fisik' => $this->stokFisik,
                'selisih' => $this->selisih,
                'tipe_adjustment' => $tipeAdjustment,
                'alasan' => $this->alasan,
                'foto_bukti' => $fotoPath,
                'created_by' => Auth::id(),
                'id_barang_masuk' => $idBarangMasuk,
                'id_barang_keluar' => $idBarangKeluar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->closeModal();
            session()->flash('success', 'Stock Opname berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function createBarangMasukAdjustment($noOpname, $qty): int
    {
        $barangMasukId = DB::table('stok_barang_masuk')->insertGetId([
            'no_barang_masuk' => 'ADJ-IN-' . $noOpname,
            'tanggal' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stok_barang_masuk_detail')->insert([
            'id_barang_masuk' => $barangMasukId,
            'id_barang' => $this->selectedBarang,
            'id_supplier' => 1, // Default supplier
            'qty_barang' => $qty,
            'harga_barang' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $barangMasukId;
    }

    private function createBarangKeluarAdjustment($noOpname, $qty): int
    {
        $barangKeluarId = DB::table('stok_barang_keluar')->insertGetId([
            'no_barang_keluar' => 'ADJ-OUT-' . $noOpname,
            'tanggal' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stok_barang_keluar_detail')->insert([
            'id_barang_keluar' => $barangKeluarId,
            'id_barang' => $this->selectedBarang,
            'qty_barang' => $qty,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $barangKeluarId;
    }

    public function render()
    {
        $barangs = DB::table('stok_barang')
            ->when($this->search, fn($q) => $q->where('nama_barang', 'like', "%{$this->search}%")
                ->orWhere('kode_barang', 'like', "%{$this->search}%"))
            ->orderBy('nama_barang')
            ->get();

        // Get stock calculation for display
        $barangsWithStock = $barangs->map(function ($barang) {
            $masuk = DB::table('stok_barang_masuk_detail')
                ->where('id_barang', $barang->id)
                ->sum('qty_barang');
            $keluar = DB::table('stok_barang_keluar_detail')
                ->where('id_barang', $barang->id)
                ->sum('qty_barang');
            
            $barang->stok_sistem = $masuk - $keluar;
            
            // Check if opname exists this month
            $barang->has_opname_this_month = DB::table('stok_opname')
                ->where('id_barang', $barang->id)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->exists();
            
            return $barang;
        });

        $allBarangs = DB::table('stok_barang')->orderBy('nama_barang')->get();

        return view('livewire.stok-opname.index', [
            'barangsWithStock' => $barangsWithStock,
            'allBarangs' => $allBarangs,
        ])->layout('components.layouts.app', ['title' => 'Stock Opname']);
    }
}
