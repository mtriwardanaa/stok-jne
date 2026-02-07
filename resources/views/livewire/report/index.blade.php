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
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan & Analisis</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Pusat data, stok opname, dan rekapitulasi transaksi.</p>
                    </div>
                </div>

                 <!-- Report Type Tabs -->
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach([
                        'stock' => 'Stok Barang',
                        'masuk' => 'Barang Masuk',
                        'keluar' => 'Barang Keluar',
                        'order' => 'Order',
                        'summary' => 'Summary Pengeluaran',
                        'opname' => 'Stok Opname'
                    ] as $key => $label)
                         <button wire:click="$set('reportType', '{{ $key }}')" class="relative px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $reportType === $key ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        @if(in_array($reportType, ['masuk', 'keluar', 'order', 'stock']))
            <!-- Filter for Masuk/Keluar/Order/Stock -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                @if($reportType !== 'stock')
                <div class="flex items-center gap-3">
                    <select wire:model.live="month" class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-sm hover:border-slate-300 transition-colors">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="year" class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-sm hover:border-slate-300 transition-colors">
                        @foreach(range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="ml-auto">
                    <button wire:click="exportExcel" class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </button>
                </div>
            </div>
        @endif

        @if($reportType === 'summary')
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
        @endif
    
        @if($reportType === 'opname')
            <!-- Stock Opname Section -->
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-sm p-6 mb-6">
                 <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                    Parameter Opname
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bulan</label>
                        <select wire:model.live="month" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                        <select wire:model.live="year" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(range(now()->year, now()->year - 3) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Koordinator GA</label>
                        <input type="text" wire:model="koordinatorGA" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama Koordinator">
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Audit Internal</label>
                        <input type="text" wire:model="auditInternal" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama Auditor">
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
    
        @if($reportType === 'stock')
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                             <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                             <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Nilai Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 pl-8 font-mono text-xs text-slate-500">{{ $item->kode_barang }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $item->nama_barang }}</td>
                                <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $item->satuan?->nama_satuan }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $item->qty_barang }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="badge badge-{{ $item->status_color }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                 <td class="px-6 py-4 text-right font-mono text-sm text-slate-700">
                                    Rp {{ number_format($item->harga_barang * $item->qty_barang, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada data stok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    
        @if($reportType === 'masuk')
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">No. Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nilai</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 pl-8 font-medium text-slate-800">{{ $item->no_barang_masuk }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md font-bold text-xs">{{ $item->total_items }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm text-slate-800">Rp {{ number_format($item->total_value, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->createdUser?->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada data barang masuk</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    
        @if($reportType === 'keluar')
             <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">No. Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Order Ref</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dibuat Oleh</th>
                        </tr>
                    </thead>
                     <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 pl-8 font-medium text-slate-800">{{ $item->no_barang_keluar }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @if($item->order)
                                        <span class="text-indigo-600 font-medium bg-indigo-50 px-2 py-1 rounded">{{ $item->order->no_order }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Manual</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-1 bg-rose-50 text-rose-600 rounded-md font-bold text-xs">{{ $item->total_items }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->createdUser?->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada data barang keluar</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    
        @if($reportType === 'order')
             <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">No Order</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pemohon</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Items</th>
                             <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 pl-8 font-medium text-slate-800">{{ $item->no_order }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->nama_user_request ?? $item->createdUser?->name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-1 bg-blue-50 text-blue-600 rounded-md font-bold text-xs">{{ $item->total_items }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="badge badge-{{ $item->status_color }}">{{ $item->status_label }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada data order</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
</div>
