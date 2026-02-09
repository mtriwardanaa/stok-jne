<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    barangMasuks: Object,
    filters: Object,
})

const search = ref(props.filters.search)
const month = ref(props.filters.month)
const year = ref(props.filters.year)

const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

const years = Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i)

let searchTimeout = null
watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get('/barang-masuk', { search: value, month: month.value, year: year.value }, { preserveState: true, preserveScroll: true })
    }, 300)
})

const applyFilter = () => {
    router.get('/barang-masuk', { search: search.value, month: month.value, year: year.value }, { preserveState: true, preserveScroll: true })
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(value)

const getTotalValue = (details) => {
    return details.reduce((sum, d) => sum + (d.qty_barang * d.harga_barang), 0)
}
</script>

<template>
    <AppLayout title="Barang Masuk">
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="relative rounded-2xl bg-white border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-emerald-500/5 via-teal-500/5 to-cyan-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-green-500/5 to-emerald-500/5 rounded-full blur-2xl -ml-32 -mb-32"></div>
                
                <div class="relative p-6">
                    <!-- Title Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl blur opacity-40"></div>
                                <div class="relative p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 bg-clip-text text-transparent">Barang Masuk</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Riwayat penerimaan barang ke gudang</p>
                            </div>
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" v-model="search" placeholder="Cari no. barang masuk..." 
                                    class="pl-10 pr-4 py-2.5 w-56 border-0 bg-slate-100/80 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/30 focus:bg-white transition-all placeholder:text-slate-400">
                            </div>
                            <div class="w-40">
                                <SearchableSelect v-model="month" :options="months" placeholder="📅 Bulan" @update:modelValue="applyFilter" />
                            </div>
                            <div class="w-28">
                                <SearchableSelect v-model="year" :options="years.map(y => ({ value: y, label: String(y) }))" placeholder="Tahun" @update:modelValue="applyFilter" />
                            </div>
                            <Link href="/barang-masuk/create" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-sm font-semibold hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-500/25 flex items-center gap-2 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">No. Barang Masuk</span>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</span>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Dibuat oleh</span>
                                </th>
                                <th class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Items</span>
                                </th>
                                <th class="px-6 py-4 text-right">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Nilai</span>
                                </th>
                                <th class="px-6 py-4 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="bm in barangMasuks.data" :key="bm.id" 
                                class="group hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-teal-50/30 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-800">{{ bm.no_barang_masuk }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-500">{{ formatDate(bm.tanggal) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ (bm.created_user?.name || 'U').substring(0, 2).toUpperCase() }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700">{{ bm.created_user?.name || '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full text-xs font-bold shadow-sm shadow-emerald-500/25">
                                        {{ bm.details?.length || 0 }} item
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-700">Rp {{ formatCurrency(getTotalValue(bm.details || [])) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="`/barang-masuk/${bm.id}`" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200 group-hover:text-emerald-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="barangMasuks.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">Tidak ada data barang masuk</p>
                                        <p class="text-sm text-slate-400 mt-1">Coba ubah filter pencarian</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
