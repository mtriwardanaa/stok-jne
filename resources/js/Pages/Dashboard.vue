<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { onMounted, ref } from 'vue'

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    lowStockItems: Array,
    chartTrend: Object,
    chartOrderStatus: Object,
    topBarangKeluar: Object,
    stokPerSatuan: Object,
})

// Chart instances
const trendChart = ref(null)
const orderStatusChart = ref(null)
const topBarangChart = ref(null)
const stokSatuanChart = ref(null)

onMounted(async () => {
    // Dynamically import Chart.js
    const { Chart, registerables } = await import('chart.js')
    Chart.register(...registerables)
    
    Chart.defaults.font.family = "'Inter', sans-serif"
    Chart.defaults.color = '#64748b'
    
    initTrendChart(Chart)
    initOrderStatusChart(Chart)
    initTopBarangChart(Chart)
    initStokSatuanChart(Chart)
})

const initTrendChart = (Chart) => {
    const ctx = document.getElementById('trendChart')?.getContext('2d')
    if (!ctx) return
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.chartTrend.labels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: props.chartTrend.masuk,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Barang Keluar',
                    data: props.chartTrend.keluar,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Order',
                    data: props.chartTrend.orders,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                x: { grid: { display: false } }
            }
        }
    })
}

const initOrderStatusChart = (Chart) => {
    const ctx = document.getElementById('orderStatusChart')?.getContext('2d')
    if (!ctx) return
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: props.chartOrderStatus.labels,
            datasets: [{
                data: props.chartOrderStatus.data,
                backgroundColor: props.chartOrderStatus.colors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    })
}

const initTopBarangChart = (Chart) => {
    const ctx = document.getElementById('topBarangChart')?.getContext('2d')
    if (!ctx) return
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.topBarangKeluar.labels,
            datasets: [{
                label: 'Jumlah Keluar',
                data: props.topBarangKeluar.data,
                backgroundColor: '#8b5cf6',
                borderRadius: 4,
                barThickness: 16
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                y: { grid: { display: false } }
            }
        }
    })
}

const initStokSatuanChart = (Chart) => {
    const ctx = document.getElementById('stokSatuanChart')?.getContext('2d')
    if (!ctx) return
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: props.stokPerSatuan.labels,
            datasets: [{
                data: props.stokPerSatuan.data,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 15, font: { size: 10 } }
                }
            }
        }
    })
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
}

const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID').format(value)
}
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Barang -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Item</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ formatNumber(stats.total_barang) }}</p>
                        </div>
                        <div class="p-2 bg-indigo-50 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs">
                        <span class="text-emerald-500 font-medium">Aktif</span>
                        <span class="text-slate-400 mx-1.5">•</span>
                        <span class="text-slate-500">Gudang Utama</span>
                    </div>
                </div>

                <!-- Stok Warning -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Stok Menipis</p>
                            <p class="text-2xl font-bold text-amber-500 mt-1">{{ formatNumber(stats.stok_warning) }}</p>
                        </div>
                        <div class="p-2 bg-amber-50 rounded-lg">
                            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-slate-500">Perlu restock segera</div>
                </div>

                <!-- Stok Habis -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Stok Habis</p>
                            <p class="text-2xl font-bold text-rose-500 mt-1">{{ formatNumber(stats.stok_habis) }}</p>
                        </div>
                        <div class="p-2 bg-rose-50 rounded-lg">
                            <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-rose-500 font-medium">Critical</div>
                </div>

                <!-- Order Pending -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Order Pending</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ formatNumber(stats.order_pending) }}</p>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <Link href="/order" class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                            Proses sekarang
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Monthly Stats Gradient Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative overflow-hidden rounded-xl p-5 bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-500/20">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10">
                        <p class="text-indigo-100 text-xs font-medium mb-1 uppercase tracking-wide">Order Bulan Ini</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-2xl font-bold">{{ formatNumber(stats.order_bulan_ini) }}</h3>
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
                            <h3 class="text-2xl font-bold">{{ formatNumber(stats.barang_masuk_bulan_ini) }}</h3>
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
                            <h3 class="text-2xl font-bold">{{ formatNumber(stats.barang_keluar_bulan_ini) }}</h3>
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
                            <h3 class="text-xl font-bold truncate">
                                <span class="text-xs font-normal text-slate-400">Rp</span> {{ (stats.total_nilai_stok / 1000000).toFixed(1) }}M
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Trend Chart -->
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

                <!-- Order Status Chart -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-slate-800 text-sm">Status Order</h3>
                        <p class="text-xs text-slate-500">Distribusi pesanan</p>
                    </div>
                    <div class="h-48 flex items-center justify-center">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div v-for="(label, index) in chartOrderStatus.labels" :key="index" class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full mr-1.5" :style="{ backgroundColor: chartOrderStatus.colors[index] }"></span>
                            <span class="text-slate-600 truncate">{{ label }}</span>
                            <span class="ml-auto font-medium text-slate-800">{{ chartOrderStatus.data[index] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
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

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Order Terbaru</h3>
                        <p class="text-xs text-slate-500">5 permintaan terakhir</p>
                    </div>
                    <Link href="/order" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
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
                            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 font-medium text-slate-900">{{ order.no_order }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                            {{ order.user_initials }}
                                        </div>
                                        <p class="text-xs font-medium text-slate-900">{{ order.user_name }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">{{ order.tanggal }}</td>
                                <td class="px-5 py-3 text-xs text-slate-500">{{ order.items_count }} item</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize"
                                        :class="{
                                            'bg-amber-100 text-amber-800': order.status === 'menunggu',
                                            'bg-blue-100 text-blue-800': order.status === 'diproses',
                                            'bg-emerald-100 text-emerald-800': order.status === 'selesai',
                                            'bg-rose-100 text-rose-800': order.status === 'ditolak',
                                        }">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <Link :href="`/order/${order.id}`" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="recentOrders.length === 0">
                                <td colspan="6" class="px-5 py-6 text-center text-slate-500 text-sm">
                                    Belum ada order terbaru
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div v-if="lowStockItems.length > 0" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-amber-50/30">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Perhatian: Stok Menipis</h3>
                        <p class="text-xs text-slate-500">Item berikut perlu segera di-restock</p>
                    </div>
                    <Link href="/barang?filter=warning" class="text-xs font-medium text-amber-600 hover:text-amber-700 flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4">
                    <div v-for="item in lowStockItems" :key="item.id" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:border-amber-200 hover:bg-amber-50/30 transition-all">
                        <div class="w-10 h-10 rounded bg-white border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-sm">
                            {{ item.nama_barang.substring(0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate" :title="item.nama_barang">{{ item.nama_barang }}</p>
                            <p class="text-[10px] text-slate-500 mb-1">{{ item.kode_barang }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full" :style="{ width: Math.min(100, (item.qty_barang / Math.max(1, item.warning_stok)) * 100) + '%' }"></div>
                                </div>
                                <span class="text-[10px] font-bold text-amber-600">{{ item.qty_barang }} <span class="font-normal text-slate-400">/ {{ item.warning_stok }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
