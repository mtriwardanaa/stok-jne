<script setup>
import { ref, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    barangsWithStock: Array,
    allBarangs: Array,
    opnameHistory: Array,
    filters: Object,
})

const month = ref(props.filters.month)
const year = ref(props.filters.year)
const search = ref(props.filters.search)

const showModal = ref(false)
const selectedBarang = ref(null)

const form = useForm({
    id_barang: '',
    stok_fisik: 0,
    alasan: '',
    foto_bukti: null,
})

const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

const years = Array.from({ length: 3 }, (_, i) => new Date().getFullYear() - i)

const applyFilter = () => {
    router.get('/stok-opname', { month: month.value, year: year.value, search: search.value }, { preserveState: true, preserveScroll: true })
}

let searchTimeout = null
watch(search, () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilter, 300)
})

const openModal = (barang) => {
    selectedBarang.value = barang
    form.reset()
    form.id_barang = barang.id
    form.stok_fisik = barang.stok_sistem
    showModal.value = true
}

const selisih = () => form.stok_fisik - (selectedBarang.value?.stok_sistem || 0)

const handleFileChange = (e) => {
    form.foto_bukti = e.target.files[0]
}

const submit = () => {
    form.post('/stok-opname', {
        onSuccess: () => {
            showModal.value = false
        },
    })
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
    <AppLayout title="Stock Opname">
        <div class="space-y-6">
            <!-- Flash Message -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                {{ $page.props.flash.error }}
            </div>

            <!-- Header -->
            <div class="relative rounded-2xl p-6 bg-white border border-slate-200 shadow-sm">
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-gradient-to-br from-violet-500/10 to-fuchsia-500/10 rounded-full blur-3xl"></div>
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-violet-500/10 rounded-xl">
                            <svg class="w-6 h-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Stock Opname</h2>
                            <p class="text-sm text-slate-500">Sesuaikan stok fisik dengan stok sistem</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <input type="text" v-model="search" placeholder="Cari barang..." 
                            class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50">
                        <select v-model="month" @change="applyFilter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50">
                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                        <select v-model="year" @change="applyFilter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Barang List for Opname -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Pilih Barang untuk Opname</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                    <div v-for="barang in barangsWithStock" :key="barang.id" 
                        @click="!barang.has_opname_this_month && openModal(barang)"
                        class="p-4 rounded-xl border transition-all cursor-pointer"
                        :class="barang.has_opname_this_month ? 'border-slate-200 bg-slate-50 opacity-60 cursor-not-allowed' : 'border-slate-200 hover:border-violet-300 hover:bg-violet-50/50'">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ barang.nama_barang }}</p>
                                <p class="text-xs text-slate-500">{{ barang.kode_barang }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold" :class="barang.stok_sistem <= 0 ? 'text-rose-500' : 'text-slate-800'">{{ barang.stok_sistem }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Stok Sistem</p>
                            </div>
                        </div>
                        <div v-if="barang.has_opname_this_month" class="mt-3 flex items-center gap-1 text-xs text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Sudah di-opname bulan ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opname History -->
            <div v-if="opnameHistory.length > 0" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Riwayat Opname ({{ months.find(m => m.value == month)?.label }} {{ year }})</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-xs font-bold text-slate-500 uppercase">
                            <th class="px-6 py-3">No Opname</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3 text-center">Stok Sistem</th>
                            <th class="px-6 py-3 text-center">Stok Fisik</th>
                            <th class="px-6 py-3 text-center">Selisih</th>
                            <th class="px-6 py-3">Adjustment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="opname in opnameHistory" :key="opname.id">
                            <td class="px-6 py-3 font-medium text-slate-800 text-sm">{{ opname.no_opname }}</td>
                            <td class="px-6 py-3 text-sm text-slate-500">{{ formatDate(opname.tanggal) }}</td>
                            <td class="px-6 py-3 text-sm text-slate-700">{{ opname.barang?.nama_barang }}</td>
                            <td class="px-6 py-3 text-center text-sm">{{ opname.stok_sistem }}</td>
                            <td class="px-6 py-3 text-center text-sm">{{ opname.stok_fisik }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-bold" 
                                    :class="opname.selisih > 0 ? 'bg-emerald-100 text-emerald-700' : opname.selisih < 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'">
                                    {{ opname.selisih > 0 ? '+' : '' }}{{ opname.selisih }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase" 
                                    :class="opname.tipe_adjustment === 'masuk' ? 'bg-emerald-100 text-emerald-700' : opname.tipe_adjustment === 'keluar' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500'">
                                    {{ opname.tipe_adjustment }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-slate-900/60" @click="showModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Stock Opname</h3>
                    
                    <div class="mb-4 p-4 bg-slate-50 rounded-lg">
                        <p class="font-semibold text-slate-800">{{ selectedBarang?.nama_barang }}</p>
                        <p class="text-xs text-slate-500">{{ selectedBarang?.kode_barang }}</p>
                        <div class="mt-2 flex items-center gap-4">
                            <span class="text-sm text-slate-500">Stok Sistem: <strong class="text-slate-800">{{ selectedBarang?.stok_sistem }}</strong></span>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Fisik</label>
                            <input type="number" v-model="form.stok_fisik" min="0" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                            <p class="mt-1 text-sm" :class="selisih() > 0 ? 'text-emerald-600' : selisih() < 0 ? 'text-rose-600' : 'text-slate-500'">
                                Selisih: {{ selisih() > 0 ? '+' : '' }}{{ selisih() }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alasan</label>
                            <textarea v-model="form.alasan" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="Jelaskan alasan perbedaan..."></textarea>
                            <p v-if="form.errors.alasan" class="text-rose-500 text-xs mt-1">{{ form.errors.alasan }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Foto Bukti (Opsional)</label>
                            <input type="file" @change="handleFileChange" accept="image/*" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600">Batal</button>
                            <button type="submit" :disabled="form.processing || selisih() === 0" class="px-4 py-2 bg-violet-600 text-white rounded-lg font-semibold disabled:opacity-50">
                                Simpan Opname
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
