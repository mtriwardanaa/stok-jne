<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    opnameData: Array,
    filters: Object,
})

const month = ref(props.filters.month)
const year = ref(props.filters.year)
const koordinatorGA = ref('')
const auditInternal = ref('')

const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

const years = Array.from({ length: 3 }, (_, i) => new Date().getFullYear() - i)

const applyFilter = () => {
    router.get('/stok-opname/report', { month: month.value, year: year.value }, { preserveState: true, preserveScroll: true })
}

const printReport = () => {
    const params = new URLSearchParams({
        month: month.value,
        year: year.value,
        koordinator: koordinatorGA.value,
        auditor: auditInternal.value,
    })
    window.open(`/report/print-opname?${params}`, '_blank')
}
</script>

<template>
    <AppLayout title="Laporan Stock Opname">
        <div class="space-y-6">
            <!-- Header -->
            <div class="relative rounded-2xl p-6 bg-white border border-slate-200 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <Link href="/stok-opname" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </Link>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Laporan Stock Opname</h2>
                            <p class="text-sm text-slate-500">Rekapitulasi pergerakan stok bulanan</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div class="w-36">
                            <SearchableSelect v-model="month" :options="months" placeholder="Bulan" @update:modelValue="applyFilter" />
                        </div>
                        <div class="w-28">
                            <SearchableSelect v-model="year" :options="years.map(y => ({ value: y, label: String(y) }))" placeholder="Tahun" @update:modelValue="applyFilter" />
                        </div>
                        <button @click="printReport" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Print
                        </button>
                    </div>
                </div>

                <!-- Print Options -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Koordinator GA (untuk print)</label>
                        <input type="text" v-model="koordinatorGA" placeholder="Nama koordinator" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Audit Internal (untuk print)</label>
                        <input type="text" v-model="auditInternal" placeholder="Nama auditor" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-xs font-bold text-slate-500 uppercase">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">Satuan</th>
                            <th class="px-4 py-3 text-center">Stok Awal</th>
                            <th class="px-4 py-3 text-center bg-emerald-50 text-emerald-700">Masuk</th>
                            <th class="px-4 py-3 text-center bg-rose-50 text-rose-700">Keluar</th>
                            <th class="px-4 py-3 text-center">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr v-for="(item, index) in opnameData" :key="index">
                            <td class="px-4 py-3 text-slate-500">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ item.kode }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ item.nama }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ item.satuan }}</td>
                            <td class="px-4 py-3 text-center">{{ item.stok_awal }}</td>
                            <td class="px-4 py-3 text-center bg-emerald-50/50 text-emerald-700 font-medium">{{ item.masuk || 0 }}</td>
                            <td class="px-4 py-3 text-center bg-rose-50/50 text-rose-700 font-medium">{{ item.keluar || 0 }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800">{{ item.stok_akhir }}</td>
                        </tr>
                        <tr v-if="opnameData.length === 0">
                            <td colspan="8" class="px-4 py-12 text-center text-slate-500">Tidak ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
