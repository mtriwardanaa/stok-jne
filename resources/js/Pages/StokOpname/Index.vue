<script setup>
import { ref, watch } from 'vue'
import { useForm, router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    barangsWithStock: Array,
    allBarangs: Array,
    opnameHistory: Array,
    filters: Object,
})

const activeTab = ref('barang') // 'barang' or 'history'
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

            <!-- Header Card -->
            <div class="relative rounded-2xl bg-white border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-violet-500/5 via-fuchsia-500/5 to-pink-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-purple-500/5 to-violet-500/5 rounded-full blur-2xl -ml-32 -mb-32"></div>
                
                <div class="relative p-6">
                    <!-- Title Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-2xl blur opacity-40"></div>
                                <div class="relative p-3 bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-2xl shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 bg-clip-text text-transparent">Stock Opname</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Sesuaikan stok fisik dengan stok sistem</p>
                            </div>
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" v-model="search" placeholder="Cari barang..." 
                                    class="pl-10 pr-4 py-2.5 w-48 border-0 bg-slate-100/80 rounded-xl text-sm focus:ring-2 focus:ring-violet-500/30 focus:bg-white transition-all placeholder:text-slate-400">
                            </div>
                            <div class="w-36">
                                <SearchableSelect v-model="month" :options="months" placeholder="Bulan" @update:modelValue="applyFilter" />
                            </div>
                            <div class="w-28">
                                <SearchableSelect v-model="year" :options="years.map(y => ({ value: y, label: String(y) }))" placeholder="Tahun" @update:modelValue="applyFilter" />
                            </div>
                            <Link href="/stok-opname/report" class="px-4 py-2.5 bg-gradient-to-r from-violet-500 to-fuchsia-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-violet-500/30 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Laporan
                            </Link>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="mt-6 flex overflow-x-auto pb-1">
                        <div class="inline-flex p-1.5 space-x-1 bg-slate-100/80 rounded-2xl">
                            <button @click="activeTab = 'barang'"
                                class="relative flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200"
                                :class="activeTab === 'barang' 
                                    ? 'bg-white text-slate-800 shadow-lg shadow-slate-200/50 ring-1 ring-slate-200/50' 
                                    : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Pilih Barang
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold" 
                                    :class="activeTab === 'barang' ? 'bg-violet-100 text-violet-600' : 'bg-slate-200/80 text-slate-500'">
                                    {{ barangsWithStock?.length || 0 }}
                                </span>
                            </button>
                            <button @click="activeTab = 'history'"
                                class="relative flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200"
                                :class="activeTab === 'history' 
                                    ? 'bg-white text-slate-800 shadow-lg shadow-slate-200/50 ring-1 ring-slate-200/50' 
                                    : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Opname
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold" 
                                    :class="activeTab === 'history' ? 'bg-violet-100 text-violet-600' : 'bg-slate-200/80 text-slate-500'">
                                    {{ opnameHistory?.length || 0 }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Barang List -->
            <div v-show="activeTab === 'barang'" class="bg-white rounded-2xl border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    <div v-for="barang in barangsWithStock" :key="barang.id" 
                        @click="!barang.has_opname_this_month && openModal(barang)"
                        class="p-4 rounded-xl border-2 transition-all cursor-pointer"
                        :class="barang.has_opname_this_month ? 'border-slate-100 bg-slate-50/50 opacity-60 cursor-not-allowed' : 'border-slate-200 hover:border-violet-400 hover:bg-violet-50/30 hover:shadow-lg'">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ barang.nama_barang }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ barang.kode_barang }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold" :class="barang.stok_sistem <= 0 ? 'text-rose-500' : 'text-slate-800'">{{ barang.stok_sistem }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">Stok Sistem</p>
                            </div>
                        </div>
                        <div v-if="barang.has_opname_this_month" class="mt-3 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Sudah di-opname bulan ini
                        </div>
                    </div>
                    
                    <div v-if="barangsWithStock?.length === 0" class="col-span-full py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p>Tidak ada barang ditemukan</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content: History -->
            <div v-show="activeTab === 'history'" class="bg-white rounded-2xl border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div v-if="opnameHistory?.length > 0" class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-indigo-100 border-b border-indigo-200">
                            <tr>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">No Opname</th>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider text-center">Stok Sistem</th>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider text-center">Stok Fisik</th>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider text-center">Selisih</th>
                                <th class="px-6 py-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Adjustment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="opname in opnameHistory" :key="opname.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-800 text-sm">{{ opname.no_opname }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(opname.tanggal) }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ opname.barang?.nama_barang }}</td>
                                <td class="px-6 py-4 text-center text-sm font-medium">{{ opname.stok_sistem }}</td>
                                <td class="px-6 py-4 text-center text-sm font-medium">{{ opname.stok_fisik }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold" 
                                        :class="opname.selisih > 0 ? 'bg-emerald-100 text-emerald-700' : opname.selisih < 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'">
                                        {{ opname.selisih > 0 ? '+' : '' }}{{ opname.selisih }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase" 
                                        :class="opname.tipe_adjustment === 'masuk' ? 'bg-emerald-100 text-emerald-700' : opname.tipe_adjustment === 'keluar' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500'">
                                        {{ opname.tipe_adjustment }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="py-16 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>Belum ada riwayat opname untuk periode ini</p>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Stock Opname</h3>
                    
                    <div class="mb-4 p-4 bg-gradient-to-r from-violet-50 to-fuchsia-50 rounded-xl border border-violet-100">
                        <p class="font-semibold text-slate-800">{{ selectedBarang?.nama_barang }}</p>
                        <p class="text-xs text-slate-500">{{ selectedBarang?.kode_barang }}</p>
                        <div class="mt-2 flex items-center gap-4">
                            <span class="text-sm text-slate-500">Stok Sistem: <strong class="text-slate-800">{{ selectedBarang?.stok_sistem }}</strong></span>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Fisik</label>
                            <input type="number" v-model="form.stok_fisik" min="0" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all">
                            <p class="mt-2 text-sm font-medium" :class="selisih() > 0 ? 'text-emerald-600' : selisih() < 0 ? 'text-rose-600' : 'text-slate-500'">
                                Selisih: {{ selisih() > 0 ? '+' : '' }}{{ selisih() }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alasan</label>
                            <textarea v-model="form.alasan" rows="3" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all" placeholder="Jelaskan alasan perbedaan..."></textarea>
                            <p v-if="form.errors.alasan" class="text-rose-500 text-xs mt-1">{{ form.errors.alasan }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Foto Bukti (Opsional)</label>
                            <input type="file" @change="handleFileChange" accept="image/*" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="showModal = false" class="px-5 py-2.5 border-2 border-slate-200 rounded-xl text-slate-600 font-medium hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" :disabled="form.processing || selisih() === 0" class="px-5 py-2.5 bg-gradient-to-r from-violet-500 to-fuchsia-600 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/30 disabled:opacity-50 hover:shadow-xl transition-all">
                                Simpan Opname
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
