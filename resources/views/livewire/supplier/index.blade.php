<div>
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Filter + Action Row -->
    <div class="flex items-center justify-between gap-4 mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari supplier..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64">
        <button wire:click="openCreateModal" class="btn-primary text-sm py-2 px-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Supplier
        </button>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Supplier</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $supplier->nama_supplier }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="edit({{ $supplier->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete({{ $supplier->id }})" wire:confirm="Yakin hapus supplier ini?" class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-4 py-12 text-center text-gray-500">Tidak ada supplier</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($suppliers->hasPages())
            <div class="px-4 py-3 border-t">{{ $suppliers->links() }}</div>
        @endif
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50" wire:click="$set('showModal', false)"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $editMode ? 'Edit' : 'Tambah' }} Supplier</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                        <input type="text" wire:model="nama_supplier" class="w-full" placeholder="Masukkan nama supplier">
                        @error('nama_supplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
