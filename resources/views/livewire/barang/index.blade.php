<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-700 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <div class="p-2 bg-emerald-100 rounded-lg">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header & Controls -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6 relative overflow-hidden">
        <!-- Decoration Background -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
            <!-- Title & Search -->
            <div class="flex flex-col gap-4 flex-1">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Master Barang</h2>
                    <p class="text-xs text-slate-500">Kelola inventaris dan stok barang</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Search -->
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            class="block w-full pl-11 pr-4 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all text-slate-900 shadow-sm" 
                            placeholder="Cari nama, kode, atau kategori barang..." 
                            autofocus>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex items-center p-1 bg-slate-100 rounded-xl overflow-x-auto no-scrollbar sm:max-w-md">
                        @foreach([
                            ['id' => '', 'label' => 'Semua', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
                            ['id' => 'aman', 'label' => 'Aman', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['id' => 'warning', 'label' => 'Warning', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                            ['id' => 'habis', 'label' => 'Habis', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z']
                        ] as $tab)
                        <button wire:click="$set('filter', '{{ $tab['id'] }}')" 
                            class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $filter === $tab['id'] ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' }}">
                            <svg class="w-3.5 h-3.5 {{ $filter === $tab['id'] ? 'text-indigo-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}" />
                            </svg>
                            {{ $tab['label'] }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <button wire:click="openCreateModal" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all active:scale-[0.98]">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Barang
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">Barang</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Harga</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($barangs as $barang)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 pl-8">
                                <div class="flex items-center gap-4">
                                    <!-- Item Icon Placeholder -->
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                        {{ substr($barang->nama_barang, 0, 2) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $barang->nama_barang }}</span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] font-mono font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $barang->kode_barang }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200">
                                    {{ $barang->satuan?->nama_satuan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-bold {{ $barang->qty_barang <= 0 ? 'text-rose-600' : ($barang->qty_barang <= $barang->min_qty ? 'text-amber-600' : 'text-slate-800') }}">
                                        {{ $barang->qty_barang }}
                                    </span>
                                    <!-- Simple Progress Bar for Stock -->
                                    <div class="w-16 h-1 mt-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        @php
                                            $percent = $barang->max_qty > 0 ? ($barang->qty_barang / $barang->max_qty) * 100 : 50; // Fallback helper
                                            $color = $barang->qty_barang <= 0 ? 'bg-rose-500' : ($barang->qty_barang <= $barang->min_qty ? 'bg-amber-500' : 'bg-emerald-500');
                                        @endphp
                                        <div class="h-full {{ $color }} rounded-full" style="width: {{ min(100, max(5, ($barang->qty_barang / max(1, $barang->min_qty * 3)) * 100)) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-medium text-slate-600">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusConfig = match($barang->status) {
                                        'aman' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                                        'warning' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
                                        'habis' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'dot' => 'bg-rose-500'],
                                        default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'dot' => 'bg-slate-500'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ ucfirst($barang->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $barang->id }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button wire:click="delete({{ $barang->id }})" wire:confirm="Yakin hapus barang ini?" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-6 text-slate-400 relative">
                                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <div class="absolute top-0 right-0 w-6 h-6 bg-rose-500 rounded-full border-4 border-white"></div>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak ada barang ditemukan</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mb-6">Barang yang Anda cari mungkin belum terdaftar atau tidak sesuai filter.</p>
                                    <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Barang Baru
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $barangs->links() }}
            </div>
        @endif
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>

                <!-- Modal panel -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-slate-900 mb-6 flex items-center gap-2" id="modal-title">
                                    <div class="p-2 bg-indigo-50 rounded-lg">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    {{ $editMode ? 'Edit' : 'Tambah' }} Barang
                                </h3>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Barang</label>
                                            <input type="text" wire:model="kode_barang" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="Contoh: AA001">
                                            @error('kode_barang') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Satuan</label>
                                            <select wire:model="id_satuan" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                                <option value="">Pilih Satuan</option>
                                                @foreach($satuans as $satuan)
                                                    <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_satuan') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Barang</label>
                                        <input type="text" wire:model="nama_barang" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="Masukkan nama barang">
                                        @error('nama_barang') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 text-sm">Rp</span>
                                                <input type="number" wire:model="harga_barang" class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="0">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Min Stok</label>
                                            <input type="number" wire:model="min_qty" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="10">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50/80 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="button" wire:click="save" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            {{ $editMode ? 'Simpan Perubahan' : 'Simpan Barang' }}
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
