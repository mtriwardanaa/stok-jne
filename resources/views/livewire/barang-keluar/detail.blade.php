<div>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('barang-keluar.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $barangKeluar->no_barang_keluar }}</h1>
                <p class="text-sm text-gray-500">Detail Barang Keluar</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('barang-keluar.invoice', $barangKeluar->id) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Invoice
            </a>
            <a href="{{ route('barang-keluar.surat-jalan', $barangKeluar->id) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                Surat Jalan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 text-sm">Item Barang</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase w-24">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($barangKeluar->details as $detail)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $detail->barang?->nama_barang }}</p>
                                    <p class="text-xs text-gray-500">{{ $detail->barang?->kode_barang }}</p>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $detail->qty_barang }} {{ $detail->barang?->satuan?->nama_satuan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-4 h-fit">
            <h3 class="font-semibold text-gray-900 text-sm mb-3">Informasi</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-500 text-xs">Tanggal</p>
                    <p class="font-medium text-gray-900">{{ $barangKeluar->tanggal->format('d M Y H:i') }}</p>
                </div>
                @if($barangKeluar->order)
                <div>
                    <p class="text-gray-500 text-xs">No Order</p>
                    <p class="font-medium text-gray-900">{{ $barangKeluar->order->no_order }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-500 text-xs">Penerima</p>
                    <p class="font-medium text-gray-900">{{ $barangKeluar->nama_user_request ?? '-' }}</p>
                </div>
                @if($barangKeluar->no_hp)
                <div>
                    <p class="text-gray-500 text-xs">No HP</p>
                    <p class="font-medium text-gray-900">{{ $barangKeluar->no_hp }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-500 text-xs">Organisasi</p>
                    <p class="font-medium text-gray-900">{{ $barangKeluar->organization_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Dibuat Oleh</p>
                    <p class="font-medium text-gray-900">{{ $barangKeluar->createdUser?->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
