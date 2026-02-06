<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('barang-masuk.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $barangMasuk->no_barang_masuk }}</h1>
            <p class="text-gray-500">Detail Barang Masuk</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Item Barang</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($barangMasuk->details as $detail)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $detail->barang?->nama_barang }}</p>
                                    <p class="text-sm text-gray-500">{{ $detail->barang?->kode_barang }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $detail->supplier?->nama_supplier }}</td>
                                <td class="px-6 py-4 text-center font-semibold">{{ $detail->qty_barang }} {{ $detail->barang?->satuan?->nama_satuan }}</td>
                                <td class="px-6 py-4 text-right text-gray-600">Rp {{ number_format($detail->harga_barang, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-700">Total</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">Rp {{ number_format($barangMasuk->total_value, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card p-6 h-fit">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="font-medium text-gray-900">{{ $barangMasuk->tanggal->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Dibuat Oleh</p>
                    <p class="font-medium text-gray-900">{{ $barangMasuk->createdUser?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Item</p>
                    <p class="font-medium text-gray-900">{{ $barangMasuk->total_items }} item</p>
                </div>
            </div>
        </div>
    </div>
</div>
