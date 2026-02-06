<div>
    @if (session('info'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">{{ session('info') }}</div>
    @endif

    <!-- Report Type Tabs -->
    <div class="flex flex-wrap gap-2 mb-4">
        <button wire:click="$set('reportType', 'stock')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $reportType === 'stock' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Stok Barang
        </button>
        <button wire:click="$set('reportType', 'masuk')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $reportType === 'masuk' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Barang Masuk
        </button>
        <button wire:click="$set('reportType', 'keluar')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $reportType === 'keluar' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Barang Keluar
        </button>
        <button wire:click="$set('reportType', 'order')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $reportType === 'order' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Order
        </button>
        <button wire:click="$set('reportType', 'summary')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $reportType === 'summary' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Summary Pengeluaran
        </button>
        <button wire:click="$set('reportType', 'opname')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $reportType === 'opname' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Stok Opname
        </button>
    </div>

    @if(in_array($reportType, ['masuk', 'keluar', 'order']))
        <!-- Filter for Masuk/Keluar/Order -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <select wire:model.live="month" class="w-40">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
                <select wire:model.live="year" class="w-28">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="exportExcel" class="btn-primary bg-green-600 hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </button>
        </div>
    @endif

    @if($reportType === 'summary')
        <!-- Summary Report Section -->
        <div class="card p-5 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary <span class="text-gray-500 text-sm font-normal">(pengeluaran divisi/partner)</span></h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" wire:model.live="dateTo" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                    <select wire:model.live="selectedBarang" class="w-full">
                        <option value="">Semua Barang</option>
                        @foreach($barangList as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Tipe</label>
                    <select wire:model.live="summaryFilter" class="w-full">
                        <option value="all">Semua (Divisi & Partner)</option>
                        <option value="divisi">Per Divisi</option>
                        <option value="partner">Per Partner/Mitra</option>
                    </select>
                </div>
                
                @if($summaryFilter === 'divisi')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Divisi</label>
                        <select wire:model.live="selectedDivisi" class="w-full">
                            <option value="">Semua Divisi</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($summaryFilter === 'partner')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Partner/Mitra</label>
                        <select wire:model.live="selectedPartner" class="w-full">
                            <option value="">Semua Partner</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            
            <button wire:click="printSummaryReport" class="btn-primary">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Report
            </button>
        </div>

        @if(!empty($summaryData['grouped']))
            <div class="card overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Divisi/Partner</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($summaryData['grouped'] as $org)
                            @foreach($org['items'] as $item)
                                <tr class="hover:bg-gray-50">
                                    @if($loop->first)
                                        <td class="px-4 py-3 font-medium text-gray-900" rowspan="{{ count($org['items']) }}">
                                            {{ $org['name'] }}
                                            <span class="text-xs text-gray-500 block">{{ $org['type'] === 'partner' ? 'Partner' : 'Divisi' }}</span>
                                        </td>
                                    @endif
                                    <td class="px-4 py-3 text-gray-600">{{ $item['nama'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $item['qty'] }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item['nilai'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-4 py-2 text-gray-700" colspan="2">Subtotal {{ $org['name'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $org['total_qty'] }}</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($org['total_nilai'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-blue-50 font-bold text-blue-900">
                            <td class="px-4 py-3" colspan="2">GRAND TOTAL</td>
                            <td class="px-4 py-3 text-center">{{ $summaryData['total_qty'] }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($summaryData['total_nilai'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="card p-12 text-center text-gray-500">
                Tidak ada data pengeluaran untuk periode yang dipilih
            </div>
        @endif
    @endif

    @if($reportType === 'opname')
        <!-- Stock Opname Section -->
        <div class="card p-5 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stok Opname <span class="text-gray-500 text-sm font-normal">(bulanan)</span></h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select wire:model.live="month" class="w-full">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select wire:model.live="year" class="w-full">
                        @foreach(range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Koordinator GA</label>
                    <input type="text" wire:model="koordinatorGA" class="w-full" placeholder="Nama Koordinator GA">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Internal</label>
                    <input type="text" wire:model="auditInternal" class="w-full" placeholder="Nama Audit Internal">
                </div>
            </div>
            
            <button wire:click="printStokOpname" class="btn-primary">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Report
            </button>
        </div>

        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Barang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Satuan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Stok Awal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase text-green-600">Masuk</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase text-red-600">Keluar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase bg-blue-50">Stok Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($opnameData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-sm text-gray-600">{{ $item['kode'] }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item['nama'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $item['satuan'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $item['stok_awal'] }}</td>
                            <td class="px-4 py-3 text-center text-green-600 font-medium">{{ $item['masuk'] > 0 ? '+' . $item['masuk'] : '-' }}</td>
                            <td class="px-4 py-3 text-center text-red-600 font-medium">{{ $item['keluar'] > 0 ? '-' . $item['keluar'] : '-' }}</td>
                            <td class="px-4 py-3 text-center font-bold bg-blue-50">{{ $item['stok_akhir'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($reportType === 'stock')
        <div class="flex items-center justify-end mb-4">
            <button wire:click="exportExcel" class="btn-primary bg-green-600 hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </button>
        </div>
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Barang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Satuan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Stok</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-sm text-gray-600">{{ $item->kode_barang }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_barang }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 text-sm">{{ $item->satuan?->nama_satuan }}</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $item->qty_barang }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge badge-{{ $item->status_color }}">{{ ucfirst($item->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($reportType === 'masuk')
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $item->no_barang_masuk }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->total_items }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->total_value, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->createdUser?->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($reportType === 'keluar')
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $item->no_barang_keluar }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->order?->no_order ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->total_items }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->createdUser?->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($reportType === 'order')
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pemohon</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $item->no_order }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->nama_user_request ?? $item->createdUser?->name }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->total_items }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge badge-{{ $item->status_color }}">{{ $item->status_label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
