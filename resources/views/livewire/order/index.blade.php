<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order</h1>
            <p class="text-gray-500 mt-1">Kelola request order dari divisi</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no order, nama..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Month -->
            <div class="w-full lg:w-40">
                <select wire:model.live="month" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year -->
            <div class="w-full lg:w-32">
                <select wire:model.live="year" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button wire:click="$set('status', '')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ !$status ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Semua <span class="ml-1 text-sm opacity-75">({{ $statusCounts['all'] }})</span>
        </button>
        <button wire:click="$set('status', 'menunggu')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'menunggu' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Menunggu <span class="ml-1 text-sm opacity-75">({{ $statusCounts['menunggu'] }})</span>
        </button>
        <button wire:click="$set('status', 'diproses')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'diproses' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Diproses <span class="ml-1 text-sm opacity-75">({{ $statusCounts['diproses'] }})</span>
        </button>
        <button wire:click="$set('status', 'selesai')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'selesai' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Selesai <span class="ml-1 text-sm opacity-75">({{ $statusCounts['selesai'] }})</span>
        </button>
        <button wire:click="$set('status', 'ditolak')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'ditolak' ? 'bg-red-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Ditolak <span class="ml-1 text-sm opacity-75">({{ $statusCounts['ditolak'] }})</span>
        </button>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemohon</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $order->no_order }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $order->tanggal->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->nama_user_request ?? $order->createdUser?->name ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->department?->name ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full font-semibold text-gray-700">
                                    {{ $order->details->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('order.detail', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p>Tidak ada order ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
