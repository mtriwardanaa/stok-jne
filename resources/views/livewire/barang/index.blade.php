<div>
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Filter + Action Row -->
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari barang..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-48">
            <div class="flex gap-1">
                <button wire:click="$set('filter', '')" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !$filter ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Semua</button>
                <button wire:click="$set('filter', 'aman')" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $filter === 'aman' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Aman</button>
                <button wire:click="$set('filter', 'warning')" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $filter === 'warning' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Warning</button>
                <button wire:click="$set('filter', 'habis')" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $filter === 'habis' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Habis</button>
            </div>
        </div>
        <button wire:click="openCreateModal" class="btn-primary text-sm py-2 px-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Barang
        </button>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Barang</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Satuan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Stok</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($barangs as $barang)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-sm text-gray-600">{{ $barang->kode_barang }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $barang->nama_barang }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 text-sm">{{ $barang->satuan?->nama_satuan }}</td>
                        <td class="px-4 py-3 text-center font-semibold">{{ $barang->qty_barang }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 text-sm">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge badge-{{ $barang->status_color }}">{{ ucfirst($barang->status) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="edit({{ $barang->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete({{ $barang->id }})" wire:confirm="Yakin hapus barang ini?" class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Tidak ada barang</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($barangs->hasPages())
            <div class="px-4 py-3 border-t">{{ $barangs->links() }}</div>
        @endif
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50" wire:click="$set('showModal', false)"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $editMode ? 'Edit' : 'Tambah' }} Barang</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang</label>
                            <input type="text" wire:model="kode_barang" class="w-full">
                            @error('kode_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                            <select wire:model="id_satuan" class="w-full">
                                <option value="">Pilih Satuan</option>
                                @foreach($satuans as $satuan)
                                    <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                                @endforeach
                            </select>
                            @error('id_satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" wire:model="nama_barang" class="w-full">
                        @error('nama_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                            <input type="number" wire:model="harga_barang" class="w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Stok</label>
                            <input type="number" wire:model="min_qty" class="w-full">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="$set('showModal', false)" class="flex-1 btn-secondary">Batal</button>
                        <button wire:click="save" class="flex-1 btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
