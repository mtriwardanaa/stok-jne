<div>
    @if (session('error'))
        <div class="mb-3 p-2.5 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-3">
        <h1 class="text-lg font-bold text-gray-900">Tambah Barang Keluar</h1>
        <a href="{{ route('barang-keluar.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <!-- LEFT: Info Penerima -->
        <div class="lg:col-span-2">
            <div class="card p-4 h-full">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Info Penerima</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                        <input type="datetime-local" wire:model="tanggal" class="w-full text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Penerima</label>
                        <div class="flex gap-4 py-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="tipe" value="internal" class="w-4 h-4 text-blue-600">
                                <span class="text-sm">Internal</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="tipe" value="partner" class="w-4 h-4 text-green-600">
                                <span class="text-sm text-green-700 font-medium">Partner/Mitra</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tipe === 'internal' ? 'Department' : 'Group' }}</label>
                        @if($tipe === 'internal')
                            <select wire:model.live="id_department" class="w-full text-sm">
                                <option value="">Pilih Department</option>
                                @foreach($departmentList as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select wire:model.live="id_group" class="w-full text-sm">
                                <option value="">Pilih Group</option>
                                @foreach($groupList as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">User <span class="text-red-500">*</span></label>
                        <select wire:model.live="id_user" class="w-full text-sm" @if(count($userList) == 0) disabled @endif>
                            <option value="">{{ count($userList) > 0 ? 'Pilih User' : 'Pilih Dept/Group dulu' }}</option>
                            @foreach($userList as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('id_user') <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">No HP</label>
                        <input type="text" wire:model="no_hp" readonly class="w-full text-sm bg-gray-50" placeholder="-">
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Daftar Barang -->
        <div class="lg:col-span-3">
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3 pb-2 border-b">
                    <h3 class="text-sm font-semibold text-gray-700">Daftar Barang</h3>
                    <button type="button" wire:click="addItem" class="text-xs text-blue-600 hover:text-blue-700 font-medium">+ Tambah</button>
                </div>

                <div class="space-y-2">
                    @foreach($items as $index => $item)
                        <div wire:key="item-{{ $index }}" class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <select wire:model.live="items.{{ $index }}.id_barang" class="w-full text-sm py-2">
                                    <option value="">Pilih Barang</option>
                                    @foreach($barangList as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                                @error("items.{$index}.id_barang") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div class="w-16 text-center">
                                @if($item['max_qty'] > 0)
                                    <span class="text-xs text-gray-500">Stok</span>
                                    <p class="text-green-600 font-bold text-sm">{{ $item['max_qty'] }}</p>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </div>
                            <div class="w-20" x-data="{ max: {{ $item['max_qty'] ?? 0 }}, qty: {{ $item['qty_barang'] ?? 1 }} }">
                                <input type="number" 
                                       x-model.number="qty"
                                       x-on:change="qty = Math.max(1, Math.min(qty, max || 9999)); $wire.set('items.{{ $index }}.qty_barang', qty)"
                                       min="1" :max="max || 9999"
                                       class="w-full px-2 py-2 text-sm text-center border rounded-lg">
                            </div>
                            @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600 p-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            @else
                                <div class="w-6"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                    <a href="{{ route('barang-keluar.index') }}" class="btn-secondary text-sm py-2 px-4">Batal</a>
                    <button type="button" wire:click="save" class="btn-primary text-sm py-2 px-5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
