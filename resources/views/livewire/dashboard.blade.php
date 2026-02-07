<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Barang -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Item</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_barang']) }}</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-emerald-500 font-medium flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Aktif
                </span>
                <span class="text-slate-400 mx-1.5">•</span>
                <span class="text-slate-500">Gudang Utama</span>
            </div>
        </div>

        <!-- Stok Warning -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Stok Menipis</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ number_format($stats['stok_warning']) }}</p>
                </div>
                <div class="p-2 bg-amber-50 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-slate-500">Perlu restock segera</span>
            </div>
        </div>

        <!-- Stok Habis -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Stok Habis</p>
                    <p class="text-2xl font-bold text-rose-500 mt-1">{{ number_format($stats['stok_habis']) }}</p>
                </div>
                <div class="p-2 bg-rose-50 rounded-lg">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-rose-500 font-medium">Critical</span>
            </div>
        </div>

        <!-- Order Pending -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Order Pending</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['order_pending']) }}</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <a href="{{ route('order.index') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                    Proses sekarang
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Stats Gradient Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="relative overflow-hidden rounded-xl p-5 bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-500/20">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <p class="text-indigo-100 text-xs font-medium mb-1 uppercase tracking-wide">Order Bulan Ini</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-2xl font-bold">{{ number_format($stats['order_bulan_ini']) }}</h3>
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl p-5 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/20">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <p class="text-emerald-100 text-xs font-medium mb-1 uppercase tracking-wide">Barang Masuk</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-2xl font-bold">{{ number_format($stats['barang_masuk_bulan_ini']) }}</h3>
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl p-5 bg-gradient-to-br from-rose-500 via-rose-600 to-pink-600 text-white shadow-lg shadow-rose-500/20">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <p class="text-rose-100 text-xs font-medium mb-1 uppercase tracking-wide">Barang Keluar</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-2xl font-bold">{{ number_format($stats['barang_keluar_bulan_ini']) }}</h3>
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl p-5 bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 text-white shadow-lg shadow-slate-500/20">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <p class="text-slate-300 text-xs font-medium mb-1 uppercase tracking-wide">Nilai Estimasi Stok</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-xl font-bold truncate" title="Rp {{ number_format($stats['total_nilai_stok'], 0, ',', '.') }}">
                        <span class="text-xs font-normal text-slate-400">Rp</span> {{ number_format($stats['total_nilai_stok'] / 1000000, 1) }}M
                    </h3>
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Trend Chart (2/3 width) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Trend Aktivitas</h3>
                    <p class="text-xs text-slate-500">Pergerakan 6 bulan terakhir</p>
                </div>
                <div class="flex gap-2">
                    <span class="flex items-center text-[10px] text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span> Masuk
                    </span>
                    <span class="flex items-center text-[10px] text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-500 mr-1"></span> Keluar
                    </span>
                    <span class="flex items-center text-[10px] text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span> Order
                    </span>
                </div>
            </div>
            <div class="h-64 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Order Status Chart (1/3 width) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Status Order</h3>
                <p class="text-xs text-slate-500">Distribusi pesanan</p>
            </div>
            <div class="h-48 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
            <!-- Custom Legend -->
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                @foreach($chartOrderStatus['labels'] as $index => $label)
                <div class="flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full mr-1.5" style="background-color: {{ $chartOrderStatus['colors'][$index] }}"></span>
                    <span class="text-slate-600 truncate">{{ ucfirst($label) }}</span>
                    <span class="ml-auto font-medium text-slate-800">{{ $chartOrderStatus['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <!-- Top Barang Keluar -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Top 10 Barang Keluar</h3>
                <p class="text-xs text-slate-500">Item paling diminta bulan ini</p>
            </div>
            <div class="h-64">
                <canvas id="topBarangChart"></canvas>
            </div>
        </div>

        <!-- Stok per Satuan -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Komposisi Stok</h3>
                <p class="text-xs text-slate-500">Berdasarkan satuan</p>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="stokSatuanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Order Terbaru</h3>
                    <p class="text-xs text-slate-500">5 permintaan terakhir</p>
                </div>
                <a href="{{ route('order.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                            <th class="px-5 py-3">No Order</th>
                            <th class="px-5 py-3">Pemohon</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Items</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-5 py-3 font-medium text-slate-900">
                                    {{ $order->no_order }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                            {{ $order->createdUser?->initials() ?? '?' }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-slate-900">{{ $order->createdUser?->name ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $order->tanggal->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $order->details->count() }} item
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize
                                        {{ $order->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $order->status === 'diproses' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $order->status === 'ditolak' ? 'bg-rose-100 text-rose-800' : '' }}
                                    ">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('order.detail', $order->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-slate-500 text-sm">
                                    Belum ada order terbaru
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Warning List -->
        @if($lowStockItems->count() > 0)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-4">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-amber-50/30">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Perhatian: Stok Menipis</h3>
                    <p class="text-xs text-slate-500">Item berikut perlu segera di-restock</p>
                </div>
                <a href="{{ route('barang.index') }}?filter=warning" class="text-xs font-medium text-amber-600 hover:text-amber-700 flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4">
                @foreach($lowStockItems as $item)
                <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:border-amber-200 hover:bg-amber-50/30 transition-all">
                    <div class="w-10 h-10 rounded bg-white border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-sm">
                        {{ substr($item->nama_barang, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate" title="{{ $item->nama_barang }}">{{ $item->nama_barang }}</p>
                        <p class="text-[10px] text-slate-500 mb-1">{{ $item->kode_barang }}</p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full" style="width: {{ min(100, ($item->qty_barang / max(1, $item->warning_stok)) * 100) }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-amber-600">{{ $item->qty_barang }} <span class="font-normal text-slate-400">/ {{ $item->warning_stok }}</span></span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.scale.grid.color = '#f1f5f9';
    
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartTrend['labels']),
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: @json($chartTrend['masuk']),
                    borderColor: '#10b981', // emerald-500
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#fff',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Barang Keluar',
                    data: @json($chartTrend['keluar']),
                    borderColor: '#8b5cf6', // violet-500
                    backgroundColor: 'rgba(139, 92, 246, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#8b5cf6',
                    pointHoverBackgroundColor: '#8b5cf6',
                    pointHoverBorderColor: '#fff',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Order',
                    data: @json($chartTrend['orders']),
                    borderColor: '#3b82f6', // blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#fff',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }, // Custom legend used
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 8,
                    titleFont: { size: 12 },
                    bodyFont: { size: 11 },
                    presentation: 'nearest',
                    intersect: false,
                    displayColors: true,
                    usePointStyle: true,
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    border: { display: false },
                    grid: { borderDash: [2, 2] },
                    ticks: { font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false,
            }
        }
    });

    // Order Status Chart - Donut Modern
    const orderCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(orderCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartOrderStatus['labels']),
            datasets: [{
                data: @json($chartOrderStatus['data']),
                backgroundColor: [
                    '#f59e0b', // amber (menunggu)
                    '#3b82f6', // blue (diproses)
                    '#10b981', // emerald (selesai)
                    '#ef4444', // red (ditolak)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Top Barang Keluar Chart
    const topCtx = document.getElementById('topBarangChart').getContext('2d');
    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: @json($topBarangKeluar['labels']),
            datasets: [{
                label: 'Jumlah Keluar',
                data: @json($topBarangKeluar['data']),
                backgroundColor: '#8b5cf6',
                borderRadius: 4,
                barThickness: 16
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { 
                    beginAtZero: true,
                    grid: { borderDash: [2, 2] },
                    ticks: { font: { size: 10 } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // Stok per Satuan Chart
    const satuanCtx = document.getElementById('stokSatuanChart').getContext('2d');
    new Chart(satuanCtx, {
        type: 'pie',
        data: {
            labels: @json($stokPerSatuan['labels']),
            datasets: [{
                data: @json($stokPerSatuan['data']),
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                    '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 10 }
                    }
                }
            }
        }
    });
});
</script>
@endpush
