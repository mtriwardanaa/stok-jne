<div>
    <!-- Filter + Action Row -->
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
        <a href="{{ route('barang-masuk.create') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Barang Masuk
        </a>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No Barang Masuk</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Item</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Nilai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dibuat Oleh</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($barangMasuks as $bm)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $bm->no_barang_masuk }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $bm->tanggal->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge badge-blue">{{ $bm->total_items }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($bm->total_value, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $bm->createdUser?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('barang-masuk.detail', $bm->id) }}" class="text-blue-600 hover:text-blue-700 text-sm">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($barangMasuks->hasPages())
            <div class="px-4 py-3 border-t">{{ $barangMasuks->links() }}</div>
        @endif
    </div>
</div>
