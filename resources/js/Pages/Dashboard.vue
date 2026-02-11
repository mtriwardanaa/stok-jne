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
    const { Chart, registerables } = await import('chart.js')
    Chart.register(...registerables)
    
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif"
    Chart.defaults.color = '#94a3b8'
    Chart.defaults.font.weight = '500'
    
    initTrendChart(Chart)
    initOrderStatusChart(Chart)
    initTopBarangChart(Chart)
    initStokSatuanChart(Chart)
})

const initTrendChart = (Chart) => {
    const ctx = document.getElementById('trendChart')?.getContext('2d')
    if (!ctx) return
    
    const gradMasuk = ctx.createLinearGradient(0, 0, 0, 280)
    gradMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.15)')
    gradMasuk.addColorStop(1, 'rgba(16, 185, 129, 0)')
    
    const gradKeluar = ctx.createLinearGradient(0, 0, 0, 280)
    gradKeluar.addColorStop(0, 'rgba(139, 92, 246, 0.15)')
    gradKeluar.addColorStop(1, 'rgba(139, 92, 246, 0)')
    
    const gradOrder = ctx.createLinearGradient(0, 0, 0, 280)
    gradOrder.addColorStop(0, 'rgba(99, 102, 241, 0.15)')
    gradOrder.addColorStop(1, 'rgba(99, 102, 241, 0)')
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.chartTrend.labels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: props.chartTrend.masuk,
                    borderColor: '#10b981',
                    backgroundColor: gradMasuk,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                },
                {
                    label: 'Barang Keluar',
                    data: props.chartTrend.keluar,
                    borderColor: '#8b5cf6',
                    backgroundColor: gradKeluar,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#8b5cf6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                },
                {
                    label: 'Order',
                    data: props.chartTrend.orders,
                    borderColor: '#6366f1',
                    backgroundColor: gradOrder,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10, titleFont: { weight: '700' } } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.08)', borderDash: [3, 3] }, border: { display: false }, ticks: { font: { size: 11 } } },
                x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } }
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
                hoverOffset: 8,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 } }
        }
    })
}

const initTopBarangChart = (Chart) => {
    const ctx = document.getElementById('topBarangChart')?.getContext('2d')
    if (!ctx) return

    const grad = ctx.createLinearGradient(0, 0, 400, 0)
    grad.addColorStop(0, '#6366f1')
    grad.addColorStop(1, '#a78bfa')
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.topBarangKeluar.labels,
            datasets: [{
                label: 'Jumlah Keluar',
                data: props.topBarangKeluar.data,
                backgroundColor: grad,
                borderRadius: 6,
                barThickness: 14
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 } },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.08)', borderDash: [3, 3] }, border: { display: false } },
                y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } }
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
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'],
                borderWidth: 0,
                borderRadius: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 16, font: { size: 10, weight: '600' } }
                },
                tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 }
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

            <!-- ═══ STAT CARDS (Top Row) ═══ -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- Total Item -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-indigo-500/5 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl shadow-lg shadow-indigo-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-1">Total Item</p>
                        <p class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ formatNumber(stats.total_barang) }}</p>
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                Aktif
                            </span>
                            <span class="text-[10px] text-slate-400">Gudang Utama</span>
                        </div>
                    </div>
                </div>

                <!-- Stok Menipis -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-amber-500/5 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-2.5 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-lg shadow-amber-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-1">Stok Menipis</p>
                        <p class="text-3xl font-extrabold text-amber-500 tracking-tight">{{ formatNumber(stats.stok_warning) }}</p>
                        <p class="mt-3 text-[11px] text-slate-400 font-medium">Perlu restock segera</p>
                    </div>
                </div>

                <!-- Stok Habis -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-rose-500/5 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-2.5 bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl shadow-lg shadow-rose-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-1">Stok Habis</p>
                        <p class="text-3xl font-extrabold text-rose-500 tracking-tight">{{ formatNumber(stats.stok_habis) }}</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                Critical
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Pending -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-2.5 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg shadow-blue-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-1">Order Pending</p>
                        <p class="text-3xl font-extrabold text-blue-600 tracking-tight">{{ formatNumber(stats.order_pending) }}</p>
                        <div class="mt-3">
                            <Link href="/order" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                                Proses sekarang
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ MONTHLY GRADIENT CARDS ═══ -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-700 text-white shadow-lg shadow-indigo-500/15 hover:shadow-xl hover:shadow-indigo-500/25 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full blur-xl -ml-8 -mb-8"></div>
                    <div class="relative z-10">
                        <p class="text-indigo-200/80 text-[10px] font-bold mb-1.5 uppercase tracking-[0.15em]">Order Bulan Ini</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-2xl font-extrabold">{{ formatNumber(stats.order_bulan_ini) }}</h3>
                            <div class="p-2 bg-white/15 rounded-xl backdrop-blur-sm border border-white/10">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 text-white shadow-lg shadow-emerald-500/15 hover:shadow-xl hover:shadow-emerald-500/25 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full blur-xl -ml-8 -mb-8"></div>
                    <div class="relative z-10">
                        <p class="text-emerald-200/80 text-[10px] font-bold mb-1.5 uppercase tracking-[0.15em]">Barang Masuk</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-2xl font-extrabold">{{ formatNumber(stats.barang_masuk_bulan_ini) }}</h3>
                            <div class="p-2 bg-white/15 rounded-xl backdrop-blur-sm border border-white/10">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-500 via-rose-600 to-pink-700 text-white shadow-lg shadow-rose-500/15 hover:shadow-xl hover:shadow-rose-500/25 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full blur-xl -ml-8 -mb-8"></div>
                    <div class="relative z-10">
                        <p class="text-rose-200/80 text-[10px] font-bold mb-1.5 uppercase tracking-[0.15em]">Barang Keluar</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-2xl font-extrabold">{{ formatNumber(stats.barang_keluar_bulan_ini) }}</h3>
                            <div class="p-2 bg-white/15 rounded-xl backdrop-blur-sm border border-white/10">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 text-white shadow-lg shadow-slate-500/15 hover:shadow-xl hover:shadow-slate-500/25 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full blur-xl -ml-8 -mb-8"></div>
                    <div class="relative z-10">
                        <p class="text-slate-400 text-[10px] font-bold mb-1.5 uppercase tracking-[0.15em]">Nilai Estimasi Stok</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-xl font-extrabold truncate">
                                <span class="text-xs font-medium text-slate-400">Rp</span> {{ (stats.total_nilai_stok / 1000000).toFixed(1) }}M
                            </h3>
                            <div class="p-2 bg-white/15 rounded-xl backdrop-blur-sm border border-white/10">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ CHARTS ROW 1 ═══ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Trend Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-[14px] tracking-tight">Trend Aktivitas</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pergerakan 6 bulan terakhir</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Masuk
                            </span>
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-violet-500"></span> Keluar
                            </span>
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Order
                            </span>
                        </div>
                    </div>
                    <div class="h-72 w-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <!-- Order Status Chart -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
                    <div class="mb-5">
                        <h3 class="font-extrabold text-slate-800 text-[14px] tracking-tight">Status Order</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Distribusi pesanan</p>
                    </div>
                    <div class="h-48 flex items-center justify-center">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="mt-5 space-y-2">
                        <div v-for="(label, index) in chartOrderStatus.labels" :key="index" class="flex items-center gap-2 text-[11px] px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ backgroundColor: chartOrderStatus.colors[index] }"></span>
                            <span class="text-slate-500 flex-1 truncate font-medium">{{ label }}</span>
                            <span class="font-extrabold text-slate-700">{{ chartOrderStatus.data[index] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ CHARTS ROW 2 ═══ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Top Barang Keluar -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
                    <div class="mb-5">
                        <h3 class="font-extrabold text-slate-800 text-[14px] tracking-tight">Top 10 Barang Keluar</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Item paling diminta bulan ini</p>
                    </div>
                    <div class="h-72">
                        <canvas id="topBarangChart"></canvas>
                    </div>
                </div>

                <!-- Stok per Satuan -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
                    <div class="mb-5">
                        <h3 class="font-extrabold text-slate-800 text-[14px] tracking-tight">Komposisi Stok</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Berdasarkan satuan</p>
                    </div>
                    <div class="h-72 flex items-center justify-center">
                        <canvas id="stokSatuanChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- ═══ RECENT ORDERS TABLE ═══ -->
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-[14px] tracking-tight">Order Terbaru</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">5 permintaan terakhir</p>
                    </div>
                    <Link href="/order" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-indigo-600 hover:text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-all">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="px-6 py-3.5 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">No Order</th>
                                <th class="px-6 py-3.5 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">Pemohon</th>
                                <th class="px-6 py-3.5 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">Tanggal</th>
                                <th class="px-6 py-3.5 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">Items</th>
                                <th class="px-6 py-3.5 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">Status</th>
                                <th class="px-6 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <tr v-for="order in recentOrders" :key="order.id" class="group hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800 text-[13px]">{{ order.no_order }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-[9px] font-bold text-white shadow-sm shadow-indigo-500/20">
                                            {{ order.user_initials }}
                                        </div>
                                        <p class="text-[12px] font-semibold text-slate-700">{{ order.user_name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[12px] text-slate-400 font-medium">{{ order.tanggal }}</td>
                                <td class="px-6 py-4 text-[12px] text-slate-400 font-medium">{{ order.items_count }} item</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold capitalize"
                                        :class="{
                                            'bg-amber-50 text-amber-700 ring-1 ring-amber-200': order.status === 'menunggu',
                                            'bg-blue-50 text-blue-700 ring-1 ring-blue-200': order.status === 'diproses',
                                            'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200': order.status === 'selesai',
                                            'bg-rose-50 text-rose-700 ring-1 ring-rose-200': order.status === 'ditolak',
                                        }">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="`/order/${order.id}`" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="recentOrders.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <p class="text-slate-400 text-sm font-medium">Belum ada order terbaru</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ═══ LOW STOCK ALERT ═══ -->
            <div v-if="lowStockItems.length > 0" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-md shadow-amber-500/20">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-[14px] tracking-tight">Perhatian: Stok Menipis</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Item berikut perlu segera di-restock</p>
                        </div>
                    </div>
                    <Link href="/barang?filter=warning" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-600 hover:text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-50 transition-all">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
                    <div v-for="item in lowStockItems" :key="item.id" class="group flex items-center gap-3 p-4 rounded-xl border border-slate-100 bg-slate-50/30 hover:border-amber-200 hover:bg-amber-50/30 hover:shadow-sm transition-all duration-200">
                        <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-[10px] font-extrabold text-slate-500 shadow-sm group-hover:border-amber-200 group-hover:text-amber-600 transition-colors">
                            {{ item.nama_barang.substring(0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12px] font-bold text-slate-800 truncate" :title="item.nama_barang">{{ item.nama_barang }}</p>
                            <p class="text-[10px] text-slate-400 mb-1.5">{{ item.kode_barang }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all" :style="{ width: Math.min(100, (item.qty_barang / Math.max(1, item.warning_stok)) * 100) + '%' }"></div>
                                </div>
                                <span class="text-[10px] font-extrabold text-amber-600">{{ item.qty_barang }} <span class="font-medium text-slate-400">/ {{ item.warning_stok }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
