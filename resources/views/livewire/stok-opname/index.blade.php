<div>
    <div class="mb-6">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header & Controls -->
        <div class="relative rounded-3xl p-1 bg-gradient-to-b from-white to-slate-50 shadow-sm border border-slate-200 mb-8">
            <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] rounded-3xl"></div>
            <div class="relative bg-white/60 backdrop-blur-xl rounded-[1.4rem] p-6">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-amber-500/10 rounded-xl">
                                <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Stock Opname</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Penyesuaian stok sistem dengan stok fisik.</p>
                    </div>
                </div>

                <!-- Tab Buttons -->
                <div class="mt-6 flex flex-wrap gap-2">
                    <button wire:click="$set('activeTab', 'buat')" class="relative px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $activeTab === 'buat' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200' }}">
                        Buat Opname
                    </button>
                    <button wire:click="$set('activeTab', 'report')" class="relative px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $activeTab === 'report' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200' }}">
                        Laporan Stok Opname
                    </button>
                </div>
            </div>
        </div>

        @if($activeTab === 'buat')
            <!-- Buat Opname Section -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6 justify-between items-center">
                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Cari barang..."
                           class="pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-sm w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Action Button -->
                <button wire:click="openModal" class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-amber-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 hover:bg-amber-700 hover:shadow-lg hover:shadow-amber-600/30">
                    <span class="relative flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Opname
                    </span>
                </button>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-6 py-5 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">Kode</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Sistem</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($barangsWithStock as $barang)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="px-6 py-4 pl-8">
                                        <span class="text-sm font-mono font-semibold text-slate-600">{{ $barang->kode_barang }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-slate-800">{{ $barang->nama_barang }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold {{ $barang->stok_sistem < 0 ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-blue-50 text-blue-600 border border-blue-200' }}">
                                            {{ number_format($barang->stok_sistem) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($barang->has_opname_this_month)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-600 border border-green-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Sudah Opname
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <p class="text-slate-500">Data barang tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($activeTab === 'report')
            <!-- Report Section -->
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-amber-500 rounded-full"></span>
                    Parameter Opname
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bulan</label>
                        <select wire:model.live="month" class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                        <select wire:model.live="year" class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            @foreach(range(now()->year, now()->year - 3) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Koordinator GA</label>
                        <input type="text" wire:model="koordinatorGA" class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" placeholder="Nama Koordinator">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Audit Internal</label>
                        <input type="text" wire:model="auditInternal" class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" placeholder="Nama Auditor">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button wire:click="printStokOpname" class="inline-flex items-center px-6 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Awal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-emerald-600 uppercase tracking-wider">Masuk</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-rose-600 uppercase tracking-wider">Keluar</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-600 uppercase tracking-wider bg-indigo-50/50">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($opnameData as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 pl-8 font-mono text-xs text-slate-500">{{ $item['kode'] }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $item['nama'] }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 text-sm">{{ $item['satuan'] }}</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600">{{ $item['stok_awal'] }}</td>
                                <td class="px-6 py-4 text-center text-emerald-600 font-bold bg-emerald-50/30">{{ $item['masuk'] > 0 ? '+' . $item['masuk'] : '-' }}</td>
                                <td class="px-6 py-4 text-center text-rose-600 font-bold bg-rose-50/30">{{ $item['keluar'] > 0 ? '-' . $item['keluar'] : '-' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-indigo-700 bg-indigo-50/50">{{ $item['stok_akhir'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">Tidak ada data stok opname</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal Form Stock Opname -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Buat Stock Opname</h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <!-- Pilih Barang -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Barang</label>
                            <select wire:model.live="selectedBarang" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                <option value="">-- Pilih Barang --</option>
                                @foreach($allBarangs as $b)
                                    <option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama_barang }}</option>
                                @endforeach
                            </select>
                            @error('selectedBarang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Warning jika sudah opname bulan ini -->
                        @if($hasOpnameThisMonth)
                            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm">
                                ⚠️ Barang ini sudah di-opname pada {{ \Carbon\Carbon::parse($lastOpnameDate)->format('d M Y') }}. Max 1x per bulan.
                            </div>
                        @endif

                        @if($selectedBarang)
                            <!-- Stok Sistem (readonly) -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Stok Sistem</label>
                                <input type="text" value="{{ number_format($stokSistem) }}" readonly
                                       class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-700 font-mono font-bold">
                            </div>

                            <!-- Stok Fisik -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Stok Fisik (Hasil Hitung)</label>
                                <input type="number" wire:model.live="stokFisik" min="0"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                @error('stokFisik') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Selisih -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Selisih</label>
                                <div class="px-4 py-2.5 rounded-xl font-mono font-bold text-lg {{ $selisih > 0 ? 'bg-green-50 text-green-600 border border-green-200' : ($selisih < 0 ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                    {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih) }}
                                    @if($selisih > 0)
                                        <span class="text-sm font-normal">(akan ditambah via Barang Masuk)</span>
                                    @elseif($selisih < 0)
                                        <span class="text-sm font-normal">(akan dikurangi via Barang Keluar)</span>
                                    @else
                                        <span class="text-sm font-normal">(tidak ada adjustment)</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Alasan (Wajib) -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan <span class="text-red-500">*</span></label>
                                <textarea wire:model="alasan" rows="3" placeholder="Jelaskan alasan perbedaan stok..."
                                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
                                @error('alasan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Foto Bukti (Optional) -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Bukti (Opsional)</label>
                                <input type="file" wire:model="fotoBukti" accept="image/*"
                                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @error('fotoBukti') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @if($fotoBukti)
                                    <div class="mt-2">
                                        <img src="{{ $fotoBukti->temporaryUrl() }}" class="h-32 rounded-lg">
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" wire:click="closeModal" class="px-4 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    {{ $hasOpnameThisMonth || $selisih == 0 ? 'disabled' : '' }}
                                    class="px-6 py-2.5 text-sm font-bold text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan Opname
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
