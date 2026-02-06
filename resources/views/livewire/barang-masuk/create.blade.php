<div>
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('barang-masuk.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Barang Masuk</h1>
            <p class="text-gray-500">Input penerimaan barang baru</p>
        </div>
    </div>

    <div class="card p-6">
        <!-- Date -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal & Waktu</label>
            <input type="datetime-local" wire:model="tanggal" class="w-full max-w-md px-4 py-2.5 border border-gray-300 rounded-lg">
        </div>

        <!-- Items -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <label class="text-sm font-medium text-gray-700">Item Barang</label>
                <button wire:click="addItem" type="button" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ Tambah Item</button>
            </div>

            <div class="space-y-4">
                @foreach($items as $index => $item)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">Barang</label>
                                <select wire:model="items.{{ $index }}.id_barang" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">Pilih Barang</option>
                                    @foreach($barangList as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Supplier</label>
                                <select wire:model="items.{{ $index }}.id_supplier" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">Pilih Supplier</option>
                                    @foreach($supplierList as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Qty</label>
                                <input type="number" wire:model="items.{{ $index }}.qty_barang" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Harga Satuan</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" wire:model="items.{{ $index }}.harga_barang" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    @if(count($items) > 1)
                                        <button wire:click="removeItem({{ $index }})" type="button" class="p-2 text-red-500 hover:bg-red-50 rounded">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('barang-masuk.index') }}" class="btn-secondary">Batal</a>
            <button wire:click="save" class="btn-primary">Simpan</button>
        </div>
    </div>
</div>
