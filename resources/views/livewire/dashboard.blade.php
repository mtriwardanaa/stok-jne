<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Barang -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Barang</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_barang']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stok Warning -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Stok Warning</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ number_format($stats['stok_warning']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stok Habis -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Stok Habis</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($stats['stok_habis']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Order Pending -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Order Pending</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ number_format($stats['order_pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-blue-100 text-sm">Order Bulan Ini</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['order_bulan_ini']) }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6 bg-gradient-to-br from-green-500 to-green-600 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <div>
                    <p class="text-green-100 text-sm">Barang Masuk</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['barang_masuk_bulan_ini']) }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6 bg-gradient-to-br from-purple-500 to-purple-600 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                    </svg>
                </div>
                <div>
                    <p class="text-purple-100 text-sm">Barang Keluar</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['barang_keluar_bulan_ini']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Order Terbaru</h3>
                <a href="{{ route('order.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentOrders as $order)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->no_order }}</p>
                            <p class="text-sm text-gray-500">{{ $order->createdUser?->name ?? 'Unknown' }} · {{ $order->details->count() }} item</p>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span>
                            <p class="text-xs text-gray-400 mt-1">{{ $order->tanggal->format('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        Belum ada order
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Stok Menipis</h3>
                <a href="{{ route('barang.index') }}?filter=warning" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($lowStockItems as $item)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->nama_barang }}</p>
                            <p class="text-sm text-gray-500">{{ $item->kode_barang }} · {{ $item->satuan?->nama_satuan ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-{{ $item->status_color }}">
                                {{ $item->qty_barang }} / {{ $item->warning_stok }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        Semua stok aman 👍
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
