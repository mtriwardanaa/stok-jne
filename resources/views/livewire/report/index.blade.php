<div>
     <div class="mb-6">
        <!-- Header & Controls -->
        <div class="relative rounded-3xl p-1 bg-gradient-to-b from-white to-slate-50 shadow-sm border border-slate-200 mb-8">
            <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] rounded-3xl"></div>
            <div class="relative bg-white/60 backdrop-blur-xl rounded-[1.4rem] p-6">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <!-- Title & Context -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-indigo-500/10 rounded-xl">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Pengeluaran</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Rekapitulasi pengeluaran barang.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Filter Card -->
        <div class="bg-gradient-to-br from-white via-white to-indigo-50/30 rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 mb-6 relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-100/40 to-purple-100/40 rounded-full blur-3xl -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-100/30 to-cyan-100/30 rounded-full blur-2xl -ml-24 -mb-24"></div>
            
            <div class="relative z-10">
                <!-- Filter Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg shadow-indigo-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Filter Laporan</h3>
                            <p class="text-xs text-slate-500">Sesuaikan periode dan kategori data</p>
                        </div>
                    </div>
                    
                    <!-- Print Button -->
                    <button wire:click="printSummaryReport" class="group inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl hover:from-slate-700 hover:to-slate-800 transition-all shadow-lg shadow-slate-900/20 hover:shadow-xl hover:shadow-slate-900/30 hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2 group-hover:animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
                
                <!-- Filter Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Tanggal Mulai -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tanggal Mulai
                        </label>
                        <div class="relative">
                            <input type="date" wire:model.live="dateFrom" 
                                   class="w-full pl-4 pr-10 py-3 bg-white/80 backdrop-blur-sm border-2 border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all hover:border-slate-300 shadow-sm">
                        </div>
                    </div>
                    
                    <!-- Tanggal Selesai -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                            <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tanggal Selesai
                        </label>
                        <div class="relative">
                            <input type="date" wire:model.live="dateTo" 
                                   class="w-full pl-4 pr-10 py-3 bg-white/80 backdrop-blur-sm border-2 border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all hover:border-slate-300 shadow-sm">
                        </div>
                    </div>
                    
                    <!-- Tipe Filter -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Tipe Filter
                        </label>
                        <select wire:model.live="summaryFilter" 
                                class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border-2 border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all hover:border-slate-300 shadow-sm appearance-none cursor-pointer"
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%2394a3b8%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                            <option value="all">🏢 Semua (Divisi & Partner)</option>
                            <option value="divisi">🏛️ Per Divisi</option>
                            <option value="partner">🤝 Per Partner/Mitra</option>
                        </select>
                    </div>
                    
                    <!-- Barang -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Barang
                        </label>
                        <select wire:model.live="selectedBarang" 
                                class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border-2 border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all hover:border-slate-300 shadow-sm appearance-none cursor-pointer"
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%2394a3b8%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                            <option value="">📦 Semua Barang</option>
                            @foreach($barangList as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Conditional Filter Row -->
                @if($summaryFilter === 'divisi' || $summaryFilter === 'partner')
                <div class="mt-5 pt-5 border-t border-slate-200/60">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @if($summaryFilter === 'divisi')
                            <div class="group">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Pilih Divisi
                                </label>
                                <select wire:model.live="selectedDivisi" 
                                        class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border-2 border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all hover:border-slate-300 shadow-sm appearance-none cursor-pointer"
                                        style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%2394a3b8%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                                    <option value="">🏛️ Semua Divisi</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($summaryFilter === 'partner')
                            <div class="group">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Pilih Partner/Mitra
                                </label>
                                <select wire:model.live="selectedPartner" 
                                        class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border-2 border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all hover:border-slate-300 shadow-sm appearance-none cursor-pointer"
                                        style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%2394a3b8%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                                    <option value="">🤝 Semua Partner</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Result Table -->
        @if(!empty($summaryData['data']))
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-50 to-slate-100/80 border-b border-slate-200">
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-12">No</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tgl Keluar</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No BK</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Sat</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Qty</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Harga</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Nilai</th>
                                <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Penerima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($summaryData['data'] as $index => $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3 text-center text-sm text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($item['tanggal_keluar'])->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-slate-600">{{ $item['no_barang_keluar'] }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-slate-500">{{ $item['kode_barang'] }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-800">{{ $item['nama_barang'] }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600 text-center">{{ $item['satuan'] }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $item['qty'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600 text-right font-mono">{{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right font-mono text-slate-800">{{ number_format($item['nilai'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $item['penerima'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gradient-to-r from-slate-900 to-slate-800 text-white font-bold">
                                <td class="px-4 py-4" colspan="6">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        TOTAL ({{ count($summaryData['data']) }} transaksi)
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">{{ $summaryData['total_qty'] }}</td>
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4 text-right font-mono">Rp {{ number_format($summaryData['total_nilai'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-[1.5rem] border-2 border-dashed border-slate-300 p-16 text-center bg-gradient-to-br from-slate-50 to-white">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-slate-100 rounded-2xl mb-4">
                        <svg class="w-12 h-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-600 mb-1">Tidak ada data</p>
                    <p class="text-sm text-slate-500">Tidak ada data pengeluaran untuk filter yang dipilih</p>
                </div>
            </div>
        @endif
</div>
