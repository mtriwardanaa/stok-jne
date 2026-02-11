<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    invoices: Object,
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

const statusOptions = [
    { value: '', label: 'Semua Status' },
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'paid', label: 'Paid' },
]

let searchTimeout = null
watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilter()
    }, 300)
})

const applyFilter = () => {
    router.get('/invoice', {
        search: search.value,
        status: status.value,
        month: month.value,
        year: year.value,
    }, { preserveState: true, preserveScroll: true })
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
}

const getTotal = (details) => {
    return details?.reduce((sum, d) => sum + (d.qty * d.harga), 0) || 0
}

const getTotalQty = (details) => {
    return details?.reduce((sum, d) => sum + d.qty, 0) || 0
}
</script>

<template>
    <AppLayout title="Invoice">
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="relative rounded-2xl bg-white border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-amber-500/5 via-orange-500/5 to-yellow-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-amber-500/5 to-orange-500/5 rounded-full blur-2xl -ml-32 -mb-32"></div>

                <div class="relative p-6">
                    <!-- Title Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl blur opacity-40"></div>
                                <div class="relative p-3 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 bg-clip-text text-transparent">List Invoice</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Daftar invoice yang sudah di-generate dari barang keluar</p>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" v-model="search" placeholder="Cari no. invoice..."
                                    class="pl-10 pr-4 py-2.5 w-52 border-0 bg-slate-100/80 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/30 focus:bg-white transition-all placeholder:text-slate-400">
                            </div>
                            <div class="w-36">
                                <SearchableSelect v-model="month" :options="months" placeholder="📅 Bulan" @update:modelValue="applyFilter" />
                            </div>
                            <div class="w-28">
                                <SearchableSelect v-model="year" :options="years.map(y => ({ value: y, label: String(y) }))" placeholder="Tahun" @update:modelValue="applyFilter" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-100 to-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">No. Invoice</span>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Tanggal</span>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">No. Brg Keluar</span>
                                </th>
                                <th class="px-6 py-4 text-center">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Items</span>
                                </th>
                                <th class="px-6 py-4 text-right">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Total</span>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Dibuat</span>
                                </th>
                                <th class="px-6 py-4 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="inv in invoices.data" :key="inv.id"
                                class="group hover:bg-gradient-to-r hover:from-amber-50/50 hover:to-orange-50/30 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-800">{{ inv.no_invoice }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-500">{{ formatDate(inv.tanggal_invoice) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <Link v-if="inv.barang_keluar" :href="`/barang-keluar/${inv.barang_keluar.id}`"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:underline">
                                        {{ inv.barang_keluar.no_barang_keluar }}
                                    </Link>
                                    <span v-else class="text-slate-400 text-sm">-</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">
                                        {{ getTotalQty(inv.details) }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-semibold text-slate-800">{{ formatCurrency(getTotal(inv.details)) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ (inv.created_user?.name || 'U').substring(0, 2).toUpperCase() }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700">{{ inv.created_user?.name || '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a :href="`/barang-keluar/${inv.id_barang_keluar}/invoice`" target="_blank"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group-hover:text-amber-500"
                                        title="Lihat Invoice">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="invoices.data.length === 0">
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">Tidak ada invoice ditemukan</p>
                                        <p class="text-sm text-slate-400 mt-1">Invoice akan muncul setelah di-generate dari barang keluar</p>
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
