<div>
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Barang</h1>
            <p class="text-gray-500 mt-1">Kelola data barang inventaris</p>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Barang
        </button>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode/nama barang..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button wire:click="$set('filter', '')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ !$filter ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Semua</button>
                <button wire:click="$set('filter', 'aman')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filter === 'aman' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Aman</button>
                <button wire:click="$set('filter', 'warning')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filter === 'warning' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Warning</button>
                <button wire:click="$set('filter', 'habis')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filter === 'habis' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Habis</button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($barangs as $barang)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $barang->kode_barang }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $barang->nama_barang }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $barang->satuan?->nama_satuan ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="badge badge-{{ $barang->status_color }}">
                                    {{ $barang->qty_barang }} / {{ $barang->warning_stok }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-1">
                                    @if($barang->internal) <span class="badge badge-blue">INT</span> @endif
                                    @if($barang->agen) <span class="badge badge-green">AGN</span> @endif
                                    @if($barang->subagen) <span class="badge badge-yellow">SUB</span> @endif
                                    @if($barang->corporate) <span class="badge badge-gray">CRP</span> @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $barang->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $barang->id }})" wire:confirm="Yakin hapus barang ini?" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">Tidak ada barang ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $barangs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50" wire:click="$set('showModal', false)"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $editMode ? 'Edit' : 'Tambah' }} Barang</h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang</label>
                                <input type="text" wire:model="kode_barang" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                                @error('kode_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                <select wire:model="id_barang_satuan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                                    <option value="">Pilih Satuan</option>
                                    @foreach($satuans as $satuan)
                                        <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                                    @endforeach
                                </select>
                                @error('id_barang_satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                            <input type="text" wire:model="nama_barang" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            @error('nama_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                                <input type="number" wire:model="harga_barang" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warning Stok</label>
                                <input type="number" wire:model="warning_stok" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <div class="flex flex-wrap gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="internal" class="w-4 h-4 text-blue-600 rounded">
                                    <span class="text-sm text-gray-700">Internal</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="agen" class="w-4 h-4 text-blue-600 rounded">
                                    <span class="text-sm text-gray-700">Agen</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="subagen" class="w-4 h-4 text-blue-600 rounded">
                                    <span class="text-sm text-gray-700">Subagen</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="corporate" class="w-4 h-4 text-blue-600 rounded">
                                    <span class="text-sm text-gray-700">Corporate</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button wire:click="$set('showModal', false)" class="flex-1 btn-secondary">Batal</button>
                        <button wire:click="save" class="flex-1 btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
