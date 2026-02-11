<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    orders: Object,
    statusCounts: Object,
    filters: Object,
})

const search = ref(props.filters.search)
const status = ref(props.filters.status)
const month = ref(props.filters.month)
const year = ref(props.filters.year)

const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

const years = Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i)

const statusTabs = [
    { id: '', label: 'Semua', color: 'slate' },
    { id: 'menunggu', label: 'Menunggu', color: 'amber' },
    { id: 'diproses', label: 'Diproses', color: 'blue' },
    { id: 'selesai', label: 'Selesai', color: 'emerald' },
    { id: 'ditolak', label: 'Ditolak', color: 'rose' },
]

let searchTimeout = null
watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilter(), 300)
})

const applyFilter = () => {
    router.get('/order', { search: search.value, status: status.value, month: month.value, year: year.value }, { preserveState: true, preserveScroll: true })
}

const setStatus = (s) => {
    status.value = s
    applyFilter()
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

const getStatusColor = (s) => {
    const colors = {
        menunggu: 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-500/25',
        diproses: 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25',
        selesai: 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-500/25',
        ditolak: 'bg-gradient-to-r from-rose-500 to-pink-500 text-white shadow-lg shadow-rose-500/25',
    }
    return colors[s] || 'bg-slate-200 text-slate-600'
}
</script>

<template>
    <AppLayout title="Order Request">
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="relative rounded-2xl bg-white border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-pink-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-blue-500/5 to-cyan-500/5 rounded-full blur-2xl -ml-32 -mb-32"></div>
                
                <div class="relative p-6">
                    <!-- Title Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl blur opacity-40"></div>
                                <div class="relative p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 bg-clip-text text-transparent">Order Request</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Kelola permintaan barang dari unit</p>
                            </div>
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" v-model="search" placeholder="Cari order..." 
                                    class="pl-10 pr-4 py-2.5 w-56 border-0 bg-slate-100/80 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/30 focus:bg-white transition-all placeholder:text-slate-400">
                            </div>
                            <div class="w-40">
                                <SearchableSelect v-model="month" :options="months" placeholder="📅 Bulan" @update:modelValue="applyFilter" />
                            </div>
                            <div class="w-28">
                                <SearchableSelect v-model="year" :options="years.map(y => ({ value: y, label: String(y) }))" placeholder="Tahun" @update:modelValue="applyFilter" />
                            </div>
                        </div>
                    </div>

                    <!-- Status Tabs -->
                    <div class="mt-6 flex overflow-x-auto pb-1">
                        <div class="inline-flex p-1.5 space-x-1 bg-slate-100/80 rounded-2xl">
                            <button v-for="tab in statusTabs" :key="tab.id" @click="setStatus(tab.id)"
                                class="relative flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200"
                                :class="status === tab.id 
                                    ? 'bg-white text-slate-800 shadow-lg shadow-slate-200/50 ring-1 ring-slate-200/50' 
                                    : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'">
                                {{ tab.label }}
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold" 
                                    :class="status === tab.id ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-200/80 text-slate-500'">
                                    {{ statusCounts[tab.id || 'all'] || 0 }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-indigo-100 border-b border-indigo-200">
                                <th class="px-6 py-3.5 text-left">
                                    <span class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">No. Order</span>
                                </th>
                                <th class="px-6 py-3.5 text-left">
                                    <span class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Tanggal</span>
                                </th>
                                <th class="px-6 py-3.5 text-left">
                                    <span class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Pemohon</span>
                                </th>
                                <th class="px-6 py-3.5 text-center">
                                    <span class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Items</span>
                                </th>
                                <th class="px-6 py-3.5 text-center">
                                    <span class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Status</span>
                                </th>
                                <th class="px-6 py-3.5 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="order in orders.data" :key="order.id" 
                                class="group hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-purple-50/30 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-800">{{ order.no_order }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-500">{{ formatDate(order.tanggal) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ (order.nama_user_request || order.created_user?.name || 'U').substring(0, 2).toUpperCase() }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700">{{ order.nama_user_request || order.created_user?.name || '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">
                                        {{ order.details?.length || 0 }} item
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wide" :class="getStatusColor(order.status)">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="`/order/${order.id}`" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 group-hover:text-indigo-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">Tidak ada data order</p>
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
