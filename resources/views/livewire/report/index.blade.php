<div>
    @if (session('info'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">{{ session('info') }}</div>
    @endif

    <!-- Filter + Action Row -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <select wire:model.live="reportType" class="w-40">
                <option value="stock">Stok Barang</option>
                <option value="masuk">Barang Masuk</option>
                <option value="keluar">Barang Keluar</option>
                <option value="order">Order</option>
            </select>
            @if($reportType !== 'stock')
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
            @endif
        </div>
        <button wire:click="exportExcel" class="btn-primary bg-green-600 hover:bg-green-700">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export Excel
        </button>
    </div>

    <div class="card overflow-hidden">
        @if($reportType === 'stock')
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
        @elseif($reportType === 'masuk')
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
        @elseif($reportType === 'keluar')
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
        @elseif($reportType === 'order')
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
        @endif
    </div>
</div>
