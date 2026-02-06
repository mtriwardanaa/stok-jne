<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Barang Keluar</h1>
            <p class="text-gray-500 mt-1">Riwayat pengeluaran barang</p>
        </div>
        <a href="{{ route('barang-keluar.create') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Manual
        </a>
    </div>

    <div class="card p-4 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no barang keluar..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg">
            </div>
            <select wire:model.live="month" class="w-full lg:w-40 px-4 py-2.5 border border-gray-300 rounded-lg">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>
            <select wire:model.live="year" class="w-full lg:w-32 px-4 py-2.5 border border-gray-300 rounded-lg">
                @foreach(range(now()->year, now()->year - 3) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No Barang Keluar</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No Order</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Penerima</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Jumlah Item</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($barangKeluars as $bk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $bk->no_barang_keluar }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $bk->tanggal->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $bk->order?->no_order ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $bk->nama_user_request ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="badge badge-purple">{{ $bk->total_items }} item</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('barang-keluar.detail', $bk->id) }}" class="text-blue-600 hover:text-blue-700">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($barangKeluars->hasPages())
            <div class="px-6 py-4 border-t">{{ $barangKeluars->links() }}</div>
        @endif
    </div>
</div>
