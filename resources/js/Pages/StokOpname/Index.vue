<script setup>
import { ref, computed, watch } from 'vue'
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

// Bulk opname data: track stok_fisik per barang
const bulkData = ref({})

// Initialize bulkData with current stok_sistem values
const initBulkData = () => {
    const data = {}
    props.barangsWithStock?.forEach(b => {
        data[b.id] = {
            stok_fisik: b.stok_sistem,
            changed: false,
        }
    })
    bulkData.value = data
}
initBulkData()

const alasan = ref('')

const onStokFisikChange = (barangId, stokSistem) => {
    if (bulkData.value[barangId]) {
        const val = parseInt(bulkData.value[barangId].stok_fisik) || 0
        bulkData.value[barangId].changed = val !== stokSistem
    }
}

const getSelisih = (barangId, stokSistem) => {
    const fisik = parseInt(bulkData.value[barangId]?.stok_fisik) || 0
    return fisik - stokSistem
}

// Items that have a selisih (and not already opnamed this month)
const changedItems = computed(() => {
    return props.barangsWithStock?.filter(b => {
        if (b.has_opname_this_month) return false
        const sel = getSelisih(b.id, b.stok_sistem)
        return sel !== 0
    }) || []
})

const isSubmitting = ref(false)

const submitBulk = () => {
    if (changedItems.value.length === 0) return
    if (!alasan.value || alasan.value.length < 10) return

    isSubmitting.value = true

    const items = changedItems.value.map(b => ({
        id_barang: b.id,
        stok_fisik: parseInt(bulkData.value[b.id]?.stok_fisik) || 0,
    }))

    router.post('/stok-opname/bulk', {
        alasan: alasan.value,
        items: items,
    }, {
        onFinish: () => {
            isSubmitting.value = false
        },
        onSuccess: () => {
            alasan.value = ''
            initBulkData()
        },
    })
}

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

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

// Filtered barang list for the table
const filteredBarangs = computed(() => {
    if (!search.value) return props.barangsWithStock || []
    const s = search.value.toLowerCase()
    return (props.barangsWithStock || []).filter(b =>
        b.nama_barang.toLowerCase().includes(s) || b.kode_barang?.toLowerCase().includes(s)
    )
})
</script>

<template>
    <AppLayout title="Stock Opname">
        <div class="space-y-6">
            <!-- Flash Message -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
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
                                <p class="text-sm text-slate-500 mt-0.5">Isi stok fisik langsung — bisa banyak item sekaligus</p>
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
                                Input Opname
                                <span v-if="changedItems.length > 0" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-violet-100 text-violet-600">
                                    {{ changedItems.length }} diubah
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

            <!-- Tab Content: Bulk Opname Table -->
            <div v-show="activeTab === 'barang'" class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gradient-to-r from-violet-50 to-fuchsia-50 border-b border-violet-100">
                                <tr>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider w-12">#</th>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider">Kode</th>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider">Nama Barang</th>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider text-center">Stok Sistem</th>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider text-center w-40">Stok Fisik</th>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider text-center">Selisih</th>
                                    <th class="px-5 py-4 text-[11px] font-semibold text-violet-800 uppercase tracking-wider text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="(barang, index) in filteredBarangs" :key="barang.id" 
                                    class="transition-colors"
                                    :class="{
                                        'bg-emerald-50/40': getSelisih(barang.id, barang.stok_sistem) > 0 && !barang.has_opname_this_month,
                                        'bg-rose-50/40': getSelisih(barang.id, barang.stok_sistem) < 0 && !barang.has_opname_this_month,
                                        'bg-slate-50/50 opacity-60': barang.has_opname_this_month,
                                        'hover:bg-slate-50/80': getSelisih(barang.id, barang.stok_sistem) === 0 && !barang.has_opname_this_month,
                                    }">
                                    <td class="px-5 py-3 text-sm text-slate-400">{{ index + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ barang.kode_barang }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="text-sm font-semibold text-slate-800">{{ barang.nama_barang }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="text-sm font-bold" :class="barang.stok_sistem <= 0 ? 'text-rose-500' : 'text-slate-700'">{{ barang.stok_sistem }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <input v-if="!barang.has_opname_this_month && bulkData[barang.id]"
                                            type="number" 
                                            v-model="bulkData[barang.id].stok_fisik" 
                                            @input="onStokFisikChange(barang.id, barang.stok_sistem)"
                                            min="0"
                                            class="w-24 mx-auto text-center px-3 py-2 border-2 rounded-xl text-sm font-semibold transition-all focus:ring-4 focus:ring-violet-500/10"
                                            :class="getSelisih(barang.id, barang.stok_sistem) !== 0 
                                                ? 'border-violet-400 bg-violet-50/50 text-violet-700 focus:border-violet-500' 
                                                : 'border-slate-200 text-slate-700 focus:border-violet-500'">
                                        <span v-else class="text-sm text-slate-400">—</span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span v-if="!barang.has_opname_this_month" 
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-700': getSelisih(barang.id, barang.stok_sistem) > 0,
                                                'bg-rose-100 text-rose-700': getSelisih(barang.id, barang.stok_sistem) < 0,
                                                'bg-slate-100 text-slate-400': getSelisih(barang.id, barang.stok_sistem) === 0,
                                            }">
                                            {{ getSelisih(barang.id, barang.stok_sistem) > 0 ? '+' : '' }}{{ getSelisih(barang.id, barang.stok_sistem) }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400">—</span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span v-if="barang.has_opname_this_month" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            Done
                                        </span>
                                        <span v-else-if="getSelisih(barang.id, barang.stok_sistem) !== 0" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-700 uppercase">
                                            Ada Selisih
                                        </span>
                                        <span v-else class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-400 uppercase">
                                            Sesuai
                                        </span>
                                    </td>
                                </tr>

                                <tr v-if="filteredBarangs.length === 0">
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <p>Tidak ada barang ditemukan</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Alasan & Submit Section -->
                <div v-if="changedItems.length > 0" class="bg-white rounded-2xl border border-violet-200/60 shadow-xl shadow-violet-200/30 p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-violet-100 rounded-xl flex-shrink-0">
                            <svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-slate-800">
                                {{ changedItems.length }} item siap disimpan
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Isi alasan opname lalu klik Simpan</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Opname <span class="text-rose-500">*</span></label>
                        <textarea v-model="alasan" rows="3" 
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all text-sm" 
                            placeholder="Jelaskan alasan opname (min. 10 karakter)..."></textarea>
                        <p v-if="alasan.length > 0 && alasan.length < 10" class="text-rose-500 text-xs mt-1">Minimal 10 karakter</p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-slate-500">
                            <span class="font-semibold text-violet-600">{{ changedItems.length }}</span> item dengan selisih akan disimpan
                        </p>
                        <button @click="submitBulk" 
                            :disabled="isSubmitting || changedItems.length === 0 || alasan.length < 10"
                            class="px-6 py-3 bg-gradient-to-r from-violet-500 to-fuchsia-600 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/30 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl transition-all flex items-center gap-2">
                            <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Opname' }}
                        </button>
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
    </AppLayout>
</template>
