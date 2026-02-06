<div>
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-900">Tambah Barang Keluar</h1>
        <a href="{{ route('barang-keluar.index') }}" class="text-sm text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>

    <div class="card p-5">
        <!-- Info Penerima - Compact 2 Column -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="datetime-local" wire:model="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                <div class="flex gap-4 py-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="tipe" value="internal" class="text-blue-600">
                        <span class="text-sm">Internal</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="tipe" value="partner" class="text-green-600">
                        <span class="text-sm text-green-700 font-medium">Partner/Mitra</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
            <!-- Department/Group -->
            <div>
                @if($tipe === 'internal')
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select wire:model.live="id_department" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih Department</option>
                        @foreach($departmentList as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                @else
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                    <select wire:model.live="id_group" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih Group</option>
                        @foreach($groupList as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- User -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User <span class="text-red-500">*</span></label>
                <select wire:model.live="id_user" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" @if(count($userList) == 0) disabled @endif>
                    <option value="">{{ count($userList) > 0 ? 'Pilih User' : 'Pilih Dept/Group dulu' }}</option>
                    @foreach($userList as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('id_user') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- No HP -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                <input type="text" wire:model="no_hp" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50" placeholder="-">
            </div>
        </div>

        <!-- Items Table -->
        <div class="border rounded-lg overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Barang</th>
                        <th class="px-3 py-2 text-center font-medium text-gray-700 w-24">Stok</th>
                        <th class="px-3 py-2 text-center font-medium text-gray-700 w-24">Qty</th>
                        <th class="px-3 py-2 w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($items as $index => $item)
                        <tr wire:key="item-{{ $index }}">
                            <td class="px-3 py-2">
                                <select wire:model.live="items.{{ $index }}.id_barang" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                                    <option value="">Pilih Barang</option>
                                    @foreach($barangList as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                                @error("items.{$index}.id_barang") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($item['max_qty'] > 0)
                                    <span class="badge badge-green">{{ $item['max_qty'] }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2" x-data="{ max: {{ $item['max_qty'] ?? 0 }}, qty: {{ $item['qty_barang'] ?? 1 }} }">
                                <input type="number" 
                                       x-model.number="qty"
                                       x-on:change="qty = Math.max(1, Math.min(qty, max || 9999)); $wire.set('items.{{ $index }}.qty_barang', qty)"
                                       min="1" :max="max || 9999"
                                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm text-center">
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" wire:click="addItem" class="text-sm text-blue-600 hover:text-blue-800 mb-4">+ Tambah Item</button>

        <!-- Actions -->
        <div class="flex gap-3 pt-4 border-t">
            <a href="{{ route('barang-keluar.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="button" wire:click="save" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan</button>
        </div>
    </div>
</div>
