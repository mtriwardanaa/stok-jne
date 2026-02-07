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
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Summary Pengeluaran</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Rekapitulasi pengeluaran barang per divisi/partner.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Report Section -->
        <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-sm p-6 mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                Filter Summary
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                    <input type="date" wire:model.live="dateTo" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Barang</label>
                    <select wire:model.live="selectedBarang" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Barang</option>
                        @foreach($barangList as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe Filter</label>
                    <select wire:model.live="summaryFilter" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">Semua (Divisi & Partner)</option>
                        <option value="divisi">Per Divisi</option>
                        <option value="partner">Per Partner/Mitra</option>
                    </select>
                </div>
                
                @if($summaryFilter === 'divisi')
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Divisi</label>
                        <select wire:model.live="selectedDivisi" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Divisi</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($summaryFilter === 'partner')
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Partner/Mitra</label>
                        <select wire:model.live="selectedPartner" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Partner</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            
            <div class="flex justify-end">
                <button wire:click="printSummaryReport" class="inline-flex items-center px-6 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>
        </div>

        @if(!empty($summaryData['grouped']))
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">Divisi/Partner</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider mr-4">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($summaryData['grouped'] as $org)
                            @foreach($org['items'] as $item)
                                <tr class="hover:bg-slate-50/80">
                                    @if($loop->first)
                                        <td class="px-6 py-4 pl-8 align-top bg-slate-50/30" rowspan="{{ count($org['items']) + 1 }}">
                                            <div class="font-bold text-slate-800">{{ $org['name'] }}</div>
                                            <div class="text-xs text-slate-500 mt-1 inline-block px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                                                {{ $org['type'] === 'partner' ? 'Partner' : 'Divisi' }}
                                            </div>
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $item['nama'] }}</td>
                                    <td class="px-6 py-4 text-center text-slate-700 font-bold bg-slate-50/50">{{ $item['qty'] }}</td>
                                    <td class="px-6 py-4 text-right text-slate-700 font-mono">Rp {{ number_format($item['nilai'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50 font-bold border-t border-slate-200">
                                <td class="px-6 py-3 text-slate-800 text-right" colspan="2">Subtotal {{ $org['name'] }}</td>
                                <td class="px-6 py-3 text-center text-indigo-600 bg-indigo-50/50">{{ $org['total_qty'] }}</td>
                                <td class="px-6 py-3 text-right text-indigo-600 bg-indigo-50/50 font-mono">Rp {{ number_format($org['total_nilai'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-slate-900 text-white font-bold text-lg">
                            <td class="px-6 py-5 pl-8" colspan="2">GRAND TOTAL</td>
                            <td class="px-6 py-5 text-center bg-slate-800">{{ $summaryData['total_qty'] }}</td>
                            <td class="px-6 py-5 text-right bg-slate-800 font-mono">Rp {{ number_format($summaryData['total_nilai'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-[1.5rem] border border-dashed border-slate-300 p-12 text-center text-slate-500 bg-slate-50">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="font-medium">Tidak ada data summary untuk filter yang dipilih</p>
                </div>
            </div>
        @endif
</div>
