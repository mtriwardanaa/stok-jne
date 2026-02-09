<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    barangKeluars: Object,
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
        router.get('/barang-keluar', { search: value, month: month.value, year: year.value }, { preserveState: true, preserveScroll: true })
    }, 300)
})

const applyFilter = () => {
    router.get('/barang-keluar', { search: search.value, month: month.value, year: year.value }, { preserveState: true, preserveScroll: true })
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const getTotalItems = (details) => {
    return details?.reduce((sum, d) => sum + d.qty_barang, 0) || 0
}
</script>

<template>
    <AppLayout title="Barang Keluar">
        <div class="space-y-6">
            <!-- Header -->
            <div class="relative rounded-2xl p-6 bg-white border border-slate-200 shadow-sm">
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-gradient-to-br from-rose-500/10 to-orange-500/10 rounded-full blur-3xl"></div>
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-rose-500/10 rounded-xl">
                            <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Barang Keluar</h2>
                            <p class="text-sm text-slate-500">Riwayat pengeluaran barang dari gudang</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <input type="text" v-model="search" placeholder="Cari no. barang keluar..." 
                            class="px-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-slate-50">
                        <select v-model="month" @change="applyFilter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50">
                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                        <select v-model="year" @change="applyFilter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                        <Link href="/barang-keluar/create" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium hover:bg-rose-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">No. Barang Keluar</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Dibuat oleh</th>
                            <th class="px-6 py-4">Dari Order</th>
                            <th class="px-6 py-4 text-center">Total Qty</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="bk in barangKeluars.data" :key="bk.id" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ bk.no_barang_keluar }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(bk.tanggal) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ bk.created_user?.name || '-' }}</td>
                            <td class="px-6 py-4">
                                <Link v-if="bk.order" :href="`/order/${bk.order.id}`" class="text-sm text-indigo-600 hover:text-indigo-700">
                                    {{ bk.order.no_order }}
                                </Link>
                                <span v-else class="text-slate-400 text-sm">-</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-rose-50 text-rose-700 rounded-lg text-xs font-semibold">{{ getTotalItems(bk.details) }} unit</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="`/barang-keluar/${bk.id}`" class="text-slate-400 hover:text-rose-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="barangKeluars.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada data barang keluar</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
