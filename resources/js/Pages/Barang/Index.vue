<script setup>
import { ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    barangs: Object,
    satuans: Array,
    filters: Object,
})

// Local state
const search = ref(props.filters.search)
const filter = ref(props.filters.filter)
const showModal = ref(false)
const editMode = ref(false)

// Form
const form = useForm({
    id: null,
    kode_barang: '',
    nama_barang: '',
    id_barang_satuan: '',
    harga_barang: 0,
    warning_stok: 10,
    internal: false,
    agen: false,
    subagen: false,
    corporate: false,
})

// Debounced search
let searchTimeout = null
watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get('/barang', { search: value, filter: filter.value }, { preserveState: true, preserveScroll: true })
    }, 300)
})

// Filter change
const setFilter = (value) => {
    filter.value = value
    router.get('/barang', { search: search.value, filter: value }, { preserveState: true, preserveScroll: true })
}

// Open modal for create
const openCreateModal = () => {
    form.reset()
    editMode.value = false
    showModal.value = true
}

// Open modal for edit
const openEditModal = (barang) => {
    form.id = barang.id
    form.kode_barang = barang.kode_barang
    form.nama_barang = barang.nama_barang
    form.id_barang_satuan = barang.id_barang_satuan
    form.harga_barang = barang.harga_barang
    form.warning_stok = barang.warning_stok
    form.internal = barang.internal
    form.agen = barang.agen
    form.subagen = barang.subagen
    form.corporate = barang.corporate
    editMode.value = true
    showModal.value = true
}

// Save
const save = () => {
    if (editMode.value) {
        form.put(`/barang/${form.id}`, {
            onSuccess: () => {
                showModal.value = false
            }
        })
    } else {
        form.post('/barang', {
            onSuccess: () => {
                showModal.value = false
            }
        })
    }
}

// Delete
const deleteBarang = (id) => {
    if (confirm('Yakin hapus barang ini?')) {
        router.delete(`/barang/${id}`)
    }
}

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID').format(value)
}

// Get status
const getStatus = (barang) => {
    if (barang.qty_barang <= 0) return 'habis'
    if (barang.qty_barang <= barang.warning_stok) return 'warning'
    return 'aman'
}

const statusConfig = {
    aman: { bg: 'bg-emerald-50', text: 'text-emerald-700', dot: 'bg-emerald-500', border: 'border-emerald-200' },
    warning: { bg: 'bg-amber-50', text: 'text-amber-700', dot: 'bg-amber-500', border: 'border-amber-200' },
    habis: { bg: 'bg-rose-50', text: 'text-rose-700', dot: 'bg-rose-500', border: 'border-rose-200' },
}

const filterTabs = [
    { id: '', label: 'Semua Barang' },
    { id: 'aman', label: 'Stok Aman' },
    { id: 'warning', label: 'Stok Menipis' },
    { id: 'habis', label: 'Stok Habis' },
]
</script>

<template>
    <AppLayout title="Master Barang">
        <div class="space-y-6">
            <!-- Flash Message -->
            <div v-if="$page.props.flash?.success" class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 flex items-center gap-3 text-emerald-700">
                <div class="p-2 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Header & Controls -->
            <div class="relative rounded-3xl p-1 bg-gradient-to-b from-white to-slate-50 shadow-sm border border-slate-200">
                <div class="relative bg-white/60 backdrop-blur-xl rounded-[1.4rem] p-6">
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2.5 bg-indigo-500/10 rounded-xl">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Master Barang</h2>
                            </div>
                            <p class="text-sm text-slate-500 ml-1">Kelola data inventaris, stok, dan harga barang.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 flex-1 justify-end">
                            <!-- Search Box -->
                            <div class="relative group w-full sm:w-72">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl opacity-20 group-hover:opacity-40 transition duration-500 blur"></div>
                                <div class="relative flex items-center bg-white rounded-xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" 
                                        v-model="search"
                                        class="block w-full pl-11 pr-4 py-3 bg-transparent border-0 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-0 sm:text-sm" 
                                        placeholder="Cari barang...">
                                </div>
                            </div>

                            <!-- Action Button -->
                            <button @click="openCreateModal" class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl transition-all">
                                <div class="absolute -inset-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 opacity-20 blur-lg group-hover:opacity-40"></div>
                                <span class="relative flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Barang
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="mt-6 flex overflow-x-auto pb-1 sm:pb-0">
                        <div class="flex p-1.5 space-x-1 bg-slate-100/60 rounded-xl border border-slate-200/60">
                            <button v-for="tab in filterTabs" :key="tab.id" @click="setFilter(tab.id)"
                                class="relative flex items-center px-4 py-2 text-xs font-bold rounded-lg transition-all"
                                :class="filter === tab.id ? 'text-indigo-600 shadow-sm bg-white ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'">
                                {{ tab.label }}
                                <span v-if="filter === tab.id" class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-indigo-500 rounded-full"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-6 py-5 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">Informasi Barang</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Ketersediaan</th>
                                <th class="px-6 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr v-for="barang in barangs.data" :key="barang.id" class="group hover:bg-slate-50/80 transition-all duration-200">
                                <td class="px-6 py-4 pl-8">
                                    <div class="flex items-center gap-4">
                                        <div class="relative w-12 h-12">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-100 to-white rounded-2xl transform rotate-3 group-hover:rotate-6 transition-transform"></div>
                                            <div class="absolute inset-0 bg-white border border-slate-100 rounded-2xl shadow-sm flex items-center justify-center text-indigo-600 font-bold text-sm transform -rotate-3 group-hover:rotate-0 transition-transform z-10">
                                                {{ barang.nama_barang.substring(0, 2) }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors line-clamp-1">{{ barang.nama_barang }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-slate-100 text-slate-500 border border-slate-200 w-fit mt-1">{{ barang.kode_barang }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200/60 shadow-sm">
                                        {{ barang.satuan?.nama_satuan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-bold" :class="{ 'text-rose-600': barang.qty_barang <= 0, 'text-amber-600': barang.qty_barang > 0 && barang.qty_barang <= barang.warning_stok, 'text-slate-800': barang.qty_barang > barang.warning_stok }">
                                            {{ formatCurrency(barang.qty_barang) }}
                                        </span>
                                        <div class="w-20 h-1.5 mt-2 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full rounded-full" 
                                                :class="{ 'bg-rose-500': barang.qty_barang <= 0, 'bg-amber-500': barang.qty_barang > 0 && barang.qty_barang <= barang.warning_stok, 'bg-gradient-to-r from-emerald-500 to-teal-400': barang.qty_barang > barang.warning_stok }"
                                                :style="{ width: Math.min(100, Math.max(5, (barang.qty_barang / Math.max(1, barang.warning_stok * 3)) * 100)) + '%' }">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-medium text-slate-700 text-sm">
                                        <span class="text-xs text-slate-400 mr-0.5">Rp</span>{{ formatCurrency(barang.harga_barang) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shadow-sm"
                                        :class="[statusConfig[getStatus(barang)].bg, statusConfig[getStatus(barang)].text, statusConfig[getStatus(barang)].border]">
                                        <span class="relative flex h-2 w-2">
                                            <span v-if="getStatus(barang) !== 'aman'" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="statusConfig[getStatus(barang)].dot"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2" :class="statusConfig[getStatus(barang)].dot"></span>
                                        </span>
                                        {{ getStatus(barang).charAt(0).toUpperCase() + getStatus(barang).slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2 opacity-60 group-hover:opacity-100 transition-all">
                                        <button @click="openEditModal(barang)" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button @click="deleteBarang(barang.id)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="barangs.data.length === 0">
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="relative w-24 h-24 mb-6">
                                            <div class="absolute inset-0 bg-indigo-100 rounded-full animate-pulse"></div>
                                            <div class="absolute inset-2 bg-white rounded-full flex items-center justify-center shadow-sm">
                                                <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 mb-2">Tidak Ada Data Ditemukan</h3>
                                        <p class="text-sm text-slate-500 max-w-sm mx-auto mb-8">
                                            Barang yang Anda cari tidak tersedia. Coba kata kunci lain atau tambahkan barang baru.
                                        </p>
                                        <button @click="openCreateModal" class="inline-flex items-center px-6 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-all">
                                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Barang Baru
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="barangs.links && barangs.links.length > 3" class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">
                            Menampilkan {{ barangs.from }} - {{ barangs.to }} dari {{ barangs.total }} data
                        </p>
                        <div class="flex gap-1">
                            <template v-for="(link, index) in barangs.links" :key="index">
                                <component 
                                    :is="link.url ? 'a' : 'span'"
                                    :href="link.url"
                                    @click.prevent="link.url && router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                                    class="px-3 py-2 text-sm rounded-lg transition-all"
                                    :class="{
                                        'bg-indigo-600 text-white': link.active,
                                        'text-slate-600 hover:bg-slate-100 cursor-pointer': link.url && !link.active,
                                        'text-slate-300 cursor-not-allowed': !link.url
                                    }"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form @submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                                <div class="p-2 bg-indigo-50 rounded-lg">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                {{ editMode ? 'Edit' : 'Tambah' }} Barang
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Barang</label>
                                        <input type="text" v-model="form.kode_barang" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="AA001">
                                        <p v-if="form.errors.kode_barang" class="text-rose-500 text-xs mt-1">{{ form.errors.kode_barang }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Satuan</label>
                                        <select v-model="form.id_barang_satuan" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                            <option value="">Pilih Satuan</option>
                                            <option v-for="satuan in satuans" :key="satuan.id" :value="satuan.id">{{ satuan.nama_satuan }}</option>
                                        </select>
                                        <p v-if="form.errors.id_barang_satuan" class="text-rose-500 text-xs mt-1">{{ form.errors.id_barang_satuan }}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Barang</label>
                                    <input type="text" v-model="form.nama_barang" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="Masukkan nama barang">
                                    <p v-if="form.errors.nama_barang" class="text-rose-500 text-xs mt-1">{{ form.errors.nama_barang }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 text-sm">Rp</span>
                                            <input type="number" v-model="form.harga_barang" class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="0">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Warning Stok</label>
                                        <input type="number" v-model="form.warning_stok" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors" placeholder="10">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/80 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all disabled:opacity-50">
                                {{ editMode ? 'Simpan Perubahan' : 'Simpan Barang' }}
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
