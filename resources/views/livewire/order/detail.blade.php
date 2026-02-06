<div>
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('order.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $order->no_order }}</h1>
            <p class="text-gray-500">Detail Order</p>
        </div>
        <span class="badge badge-{{ $order->status_color }} ml-auto text-sm px-3 py-1">{{ $order->status_label }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Details -->
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Informasi Order</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Order</p>
                        <p class="font-medium text-gray-900">{{ $order->tanggal->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pemohon</p>
                        <p class="font-medium text-gray-900">{{ $order->nama_user_request ?? $order->createdUser?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No HP</p>
                        <p class="font-medium text-gray-900">{{ $order->hp_user_request ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Divisi</p>
                        <p class="font-medium text-gray-900">{{ $order->department?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Item yang Dipesan</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty Request</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Stok Tersedia</th>
                            @if($order->status === 'selesai')
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty Approved</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->details as $detail)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $detail->barang?->nama_barang ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">{{ $detail->barang?->kode_barang }} · {{ $detail->barang?->satuan?->nama_satuan }}</p>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                    {{ $detail->qty_barang }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="badge badge-{{ $detail->barang?->status_color ?? 'gray' }}">
                                        {{ $detail->barang?->qty_barang ?? 0 }}
                                    </span>
                                </td>
                                @if($order->status === 'selesai')
                                    <td class="px-6 py-4 text-center font-semibold text-green-600">
                                        {{ $detail->qty_approved ?? '-' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status History -->
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Riwayat Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Dibuat</p>
                            <p class="text-sm text-gray-500">{{ $order->createdUser?->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->tanggal->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    @if($order->tanggal_approve)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Disetujui</p>
                                <p class="text-sm text-gray-500">{{ $order->approvedUser?->name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->tanggal_approve->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->tanggal_reject)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Ditolak</p>
                                <p class="text-sm text-gray-500">{{ $order->rejectedUser?->name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->tanggal_reject->format('d M Y H:i') }}</p>
                                <p class="text-sm text-red-600 mt-1">{{ $order->rejected_text }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($order->status === 'menunggu')
                <div class="card p-6 space-y-3">
                    <button wire:click="openApproveModal" class="w-full btn-primary bg-green-600 hover:bg-green-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve Order
                    </button>
                    <button wire:click="openRejectModal" class="w-full btn-secondary text-red-600 hover:bg-red-50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tolak Order
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Approve Modal -->
    @if($showApproveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50" wire:click="$set('showApproveModal', false)"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Order</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Distribusi Sales</label>
                            <input type="text" wire:model="distribusiSales" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="Opsional">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Qty yang Disetujui</label>
                            <div class="space-y-2">
                                @foreach($order->details as $detail)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm text-gray-700">{{ $detail->barang?->nama_barang }}</span>
                                        <input type="number" wire:model="approvedQty.{{ $detail->id }}" min="0" max="{{ $detail->qty_barang }}" class="w-20 px-2 py-1 border border-gray-300 rounded text-center">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="$set('showApproveModal', false)" class="flex-1 btn-secondary">Batal</button>
                        <button wire:click="approve" class="flex-1 btn-primary bg-green-600 hover:bg-green-700">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50" wire:click="$set('showRejectModal', false)"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Order</h3>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea wire:model="rejectReason" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="Masukkan alasan penolakan..."></textarea>
                        @error('rejectReason') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="$set('showRejectModal', false)" class="flex-1 btn-secondary">Batal</button>
                        <button wire:click="reject" class="flex-1 btn-primary bg-red-600 hover:bg-red-700">Tolak</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
