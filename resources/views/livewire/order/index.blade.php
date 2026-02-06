<div>
    <!-- Filter Row - All inline -->
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no order, nama..." class="w-64">
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap gap-2 mb-4">
        <button wire:click="$set('status', '')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ !$status ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Semua <span class="opacity-75">({{ $statusCounts['all'] }})</span>
        </button>
        <button wire:click="$set('status', 'menunggu')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'menunggu' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Menunggu <span class="opacity-75">({{ $statusCounts['menunggu'] }})</span>
        </button>
        <button wire:click="$set('status', 'diproses')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'diproses' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Diproses <span class="opacity-75">({{ $statusCounts['diproses'] }})</span>
        </button>
        <button wire:click="$set('status', 'selesai')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'selesai' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Selesai <span class="opacity-75">({{ $statusCounts['selesai'] }})</span>
        </button>
        <button wire:click="$set('status', 'ditolak')" class="px-4 py-2 rounded-lg font-medium transition-colors {{ $status === 'ditolak' ? 'bg-red-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Ditolak <span class="opacity-75">({{ $statusCounts['ditolak'] }})</span>
        </button>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pemohon</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $order->no_order }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $order->tanggal->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 text-sm">{{ $order->nama_user_request ?? $order->createdUser?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $order->organization_name }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge badge-blue">{{ $order->details->count() }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('order.detail', $order->id) }}" class="text-blue-600 hover:text-blue-700 text-sm">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Tidak ada order</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
            <div class="px-4 py-3 border-t">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
