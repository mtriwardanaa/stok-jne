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
        menunggu: 'bg-amber-100 text-amber-800',
        diproses: 'bg-blue-100 text-blue-800',
        selesai: 'bg-emerald-100 text-emerald-800',
        ditolak: 'bg-rose-100 text-rose-800',
    }
    return colors[s] || 'bg-slate-100 text-slate-800'
}
</script>

<template>
    <AppLayout title="Order Request">
        <div class="space-y-6">
            <!-- Header -->
            <div class="relative rounded-2xl p-6 bg-white border border-slate-200 shadow-sm">
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl"></div>
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-500/10 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Order Request</h2>
                            <p class="text-sm text-slate-500">Kelola permintaan barang dari unit</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <input type="text" v-model="search" placeholder="Cari order..." 
                            class="px-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50">
                        <div class="w-36">
                            <SearchableSelect v-model="month" :options="months" placeholder="Bulan" @update:modelValue="applyFilter" />
                        </div>
                        <div class="w-28">
                            <SearchableSelect v-model="year" :options="years.map(y => ({ value: y, label: String(y) }))" placeholder="Tahun" @update:modelValue="applyFilter" />
                        </div>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="mt-6 flex overflow-x-auto">
                    <div class="flex p-1 space-x-1 bg-slate-100/60 rounded-xl border border-slate-200/60">
                        <button v-for="tab in statusTabs" :key="tab.id" @click="setStatus(tab.id)"
                            class="relative flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all"
                            :class="status === tab.id ? 'text-indigo-600 shadow-sm bg-white ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'">
                            {{ tab.label }}
                            <span class="px-1.5 py-0.5 rounded text-[10px]" :class="status === tab.id ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-200 text-slate-500'">
                                {{ statusCounts[tab.id || 'all'] || 0 }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">No. Order</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Pemohon</th>
                            <th class="px-6 py-4 text-center">Items</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ order.no_order }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(order.tanggal) }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-800">{{ order.nama_user_request || order.created_user?.name || '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">{{ order.details?.length || 0 }} item</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase" :class="getStatusColor(order.status)">
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="`/order/${order.id}`" class="text-slate-400 hover:text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada data order</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
