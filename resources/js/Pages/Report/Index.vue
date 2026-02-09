<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    filters: Object,
    departments: Array,
    groups: Array,
    barangList: Array,
    summaryData: Object,
})

// Local filter state
const dateFrom = ref(props.filters.dateFrom)
const dateTo = ref(props.filters.dateTo)
const summaryFilter = ref(props.filters.summaryFilter)
const selectedDivisi = ref(props.filters.selectedDivisi)
const selectedPartner = ref(props.filters.selectedPartner)
const selectedBarang = ref(props.filters.selectedBarang)

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
}

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

// Filter type options
const filterTypes = [
    { value: 'all', label: '📊 Semua Data', color: 'indigo' },
    { value: 'divisi', label: '🏛️ Per Divisi', color: 'amber' },
    { value: 'partner', label: '🤝 Per Partner/Mitra', color: 'rose' },
]

// Apply filters
const applyFilters = () => {
    router.get('/report', {
        dateFrom: dateFrom.value,
        dateTo: dateTo.value,
        summaryFilter: summaryFilter.value,
        selectedDivisi: selectedDivisi.value,
        selectedPartner: selectedPartner.value,
        selectedBarang: selectedBarang.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

// Watch for filter changes and auto-apply
watch([dateFrom, dateTo, summaryFilter, selectedDivisi, selectedPartner, selectedBarang], () => {
    applyFilters()
}, { deep: true })

// Reset sub-filters when main filter changes
watch(summaryFilter, () => {
    selectedDivisi.value = ''
    selectedPartner.value = ''
})

// Print report
const printReport = () => {
    const params = new URLSearchParams({
        dateFrom: dateFrom.value,
        dateTo: dateTo.value,
        filter: summaryFilter.value,
        divisi: selectedDivisi.value,
        partner: selectedPartner.value,
        barang: selectedBarang.value,
    })
    window.open('/report/print-summary?' + params.toString(), '_blank')
}
</script>

<template>
    <AppLayout title="Summary Pengeluaran">
        <div class="space-y-6">
            <!-- Filter Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Filter Laporan</h3>
                            <p class="text-sm text-slate-500">Pilih periode dan jenis laporan</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <!-- Date From -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Dari Tanggal
                            </label>
                            <input 
                                type="date" 
                                v-model="dateFrom"
                                class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                            >
                        </div>
                        
                        <!-- Date To -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Sampai Tanggal
                            </label>
                            <input 
                                type="date" 
                                v-model="dateTo"
                                class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                            >
                        </div>
                        
                        <!-- Summary Filter -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Jenis Laporan
                            </label>
                            <select 
                                v-model="summaryFilter"
                                class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all"
                            >
                                <option v-for="type in filterTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Barang -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Barang
                            </label>
                            <SearchableSelect 
                                v-model="selectedBarang"
                                :options="barangList"
                                placeholder="📦 Semua Barang"
                            />
                        </div>
                    </div>
                    
                    <!-- Conditional Filter Row -->
                    <div v-if="summaryFilter === 'divisi' || summaryFilter === 'partner'" class="mt-5 pt-5 border-t border-slate-200/60">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div v-if="summaryFilter === 'divisi'" class="group">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Pilih Divisi
                                </label>
                                <SearchableSelect 
                                    v-model="selectedDivisi"
                                    :options="departments"
                                    placeholder="🏛️ Semua Divisi"
                                />
                            </div>
                            <div v-else-if="summaryFilter === 'partner'" class="group">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Pilih Partner/Mitra
                                </label>
                                <SearchableSelect 
                                    v-model="selectedPartner"
                                    :options="groups"
                                    placeholder="🤝 Semua Partner"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Table -->
            <div v-if="summaryData.data.length > 0" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/60 bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-tr from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Hasil Laporan</h3>
                            <p class="text-sm text-slate-500">{{ summaryData.data.length }} transaksi</p>
                        </div>
                    </div>
                    <button @click="printReport" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print PDF
                    </button>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase">No. Keluar</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase">Kode</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase">Nama Barang</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase">Penerima/Divisi/Group</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">Qty</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase text-right">Harga</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase text-right">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in summaryData.data" :key="index" class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-600">{{ formatDate(item.tanggal_keluar) }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ item.no_barang_keluar }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ item.kode_barang }}</td>
                                <td class="px-4 py-3 text-sm text-slate-800">{{ item.nama_barang }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    <div>{{ item.penerima }}</div>
                                    <div class="text-xs text-slate-400">{{ item.divisi || item.group || '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-800 text-center font-medium">{{ item.qty }} {{ item.satuan }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 text-right">{{ formatCurrency(item.harga) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-800 text-right">{{ formatCurrency(item.nilai) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gradient-to-r from-indigo-50 to-purple-50 font-bold">
                                <td colspan="5" class="px-4 py-4 text-sm text-slate-800">TOTAL</td>
                                <td class="px-4 py-4 text-sm text-slate-800 text-center">{{ summaryData.total_qty }}</td>
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4 text-sm text-indigo-700 text-right">{{ formatCurrency(summaryData.total_nilai) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 mb-1">Tidak ada data</h3>
                <p class="text-slate-500">Tidak ditemukan transaksi untuk filter yang dipilih</p>
            </div>
        </div>
    </AppLayout>
</template>
