<script setup>
import { ref, watch, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    barangs: Array,
    partners: Array,
    checkedIds: Array,
    filters: Object,
})

const search = ref(props.filters.search)
const tipe = ref(props.filters.tipe)
const localChecked = ref([...props.checkedIds])

// Watch for prop changes when filters change
watch(() => props.checkedIds, (val) => {
    localChecked.value = [...val]
})

// Switch tipe
const switchTipe = (newTipe) => {
    tipe.value = newTipe
    router.get('/ketersediaan', { tipe: newTipe, search: search.value }, { preserveState: true, preserveScroll: true })
}

// Debounced search
let searchTimeout = null
watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get('/ketersediaan', { tipe: tipe.value, search: value }, { preserveState: true, preserveScroll: true })
    }, 300)
})

// Toggle single item
const toggleItem = (barangId) => {
    const idx = localChecked.value.indexOf(barangId)
    if (idx > -1) {
        localChecked.value.splice(idx, 1)
    } else {
        localChecked.value.push(barangId)
    }
}

// Select all / deselect all
const selectAll = () => {
    localChecked.value = props.barangs.map(b => b.id)
}

const deselectAll = () => {
    localChecked.value = []
}

// Check if all selected
const allSelected = computed(() => {
    return props.barangs.length > 0 && localChecked.value.length === props.barangs.length
})

const someSelected = computed(() => {
    return localChecked.value.length > 0 && localChecked.value.length < props.barangs.length
})

// Current label
const currentLabel = computed(() => {
    if (tipe.value === 'internal') return 'Internal (Semua Department)'
    const partner = props.partners.find(p => String(p.id) === String(tipe.value))
    return partner?.name || 'Pilih Tipe'
})

// Save
const saving = ref(false)
const save = () => {
    saving.value = true
    router.post('/ketersediaan', {
        tipe: tipe.value,
        checked_ids: localChecked.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            saving.value = false
        }
    })
}

// Has unsaved changes
const hasChanges = computed(() => {
    if (localChecked.value.length !== props.checkedIds.length) return true
    const sorted1 = [...localChecked.value].sort()
    const sorted2 = [...props.checkedIds].sort()
    return sorted1.some((v, i) => v !== sorted2[i])
})
</script>

<template>
    <AppLayout title="Ketersediaan Barang">
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

            <!-- Header Card -->
            <div class="relative rounded-2xl bg-white border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-pink-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-indigo-500/5 to-cyan-500/5 rounded-full blur-2xl -ml-32 -mb-32"></div>

                <div class="relative p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl blur opacity-40"></div>
                                <div class="relative p-3 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 bg-clip-text text-transparent">Ketersediaan Barang</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Atur barang yang tersedia per tipe organisasi</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" v-model="search" placeholder="Cari barang..."
                                    class="pl-10 pr-4 py-2.5 w-56 border-0 bg-slate-100/80 rounded-xl text-sm focus:ring-2 focus:ring-violet-500/30 focus:bg-white transition-all placeholder:text-slate-400">
                            </div>
                        </div>
                    </div>

                    <!-- Tipe Tabs -->
                    <div class="mt-6 flex overflow-x-auto pb-1">
                        <div class="inline-flex p-1.5 space-x-1 bg-slate-100/80 rounded-2xl">
                            <button @click="switchTipe('internal')"
                                class="relative flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200"
                                :class="tipe === 'internal'
                                    ? 'bg-white text-slate-800 shadow-lg shadow-slate-200/50 ring-1 ring-slate-200/50'
                                    : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                </svg>
                                Internal
                            </button>
                            <button v-for="partner in partners" :key="partner.id" @click="switchTipe(String(partner.id))"
                                class="relative flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 whitespace-nowrap"
                                :class="String(tipe) === String(partner.id)
                                    ? 'bg-white text-slate-800 shadow-lg shadow-slate-200/50 ring-1 ring-slate-200/50'
                                    : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'">
                                {{ partner.name }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary + Actions Bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-1">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600">
                        <strong class="text-violet-600">{{ localChecked.length }}</strong> dari <strong>{{ barangs.length }}</strong> barang dipilih untuk
                        <span class="font-bold text-slate-800">{{ currentLabel }}</span>
                    </span>
                    <span v-if="hasChanges" class="px-2 py-0.5 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full animate-pulse">
                        Belum disimpan
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="selectAll" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all">
                        Pilih Semua
                    </button>
                    <button @click="deselectAll" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all">
                        Hapus Semua
                    </button>
                    <button @click="save" :disabled="saving || !hasChanges"
                        class="flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-violet-500/30 hover:shadow-xl hover:shadow-violet-500/40 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ saving ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-violet-100 border-b border-violet-200">
                                <th class="px-6 py-5 text-center w-16">
                                    <input type="checkbox" :checked="allSelected" :indeterminate="someSelected" @change="allSelected ? deselectAll() : selectAll()"
                                        class="w-4 h-4 text-violet-600 border-slate-300 rounded focus:ring-violet-500/30 cursor-pointer">
                                </th>
                                <th class="px-6 py-5 text-[11px] font-semibold text-violet-800 uppercase tracking-wider">Informasi Barang</th>
                                <th class="px-6 py-5 text-center text-[11px] font-semibold text-violet-800 uppercase tracking-wider">Satuan</th>
                                <th class="px-6 py-5 text-center text-[11px] font-semibold text-violet-800 uppercase tracking-wider">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr v-for="barang in barangs" :key="barang.id"
                                @click="toggleItem(barang.id)"
                                class="group hover:bg-violet-50/50 transition-all duration-200 cursor-pointer"
                                :class="localChecked.includes(barang.id) ? 'bg-violet-50/30' : ''">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" :checked="localChecked.includes(barang.id)" @click.stop="toggleItem(barang.id)"
                                        class="w-4 h-4 text-violet-600 border-slate-300 rounded focus:ring-violet-500/30 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative w-10 h-10">
                                            <div class="absolute inset-0 bg-white border border-slate-100 rounded-xl shadow-sm flex items-center justify-center text-violet-600 font-bold text-xs">
                                                {{ barang.nama_barang.substring(0, 2) }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800 group-hover:text-violet-600 transition-colors line-clamp-1">{{ barang.nama_barang }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-slate-100 text-slate-500 border border-slate-200 w-fit mt-1">{{ barang.kode_barang }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200/60 shadow-sm">
                                        {{ barang.satuan?.nama_satuan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold" :class="{
                                        'text-rose-600': barang.qty_barang <= 0,
                                        'text-amber-600': barang.qty_barang > 0 && barang.qty_barang <= barang.warning_stok,
                                        'text-slate-800': barang.qty_barang > barang.warning_stok
                                    }">
                                        {{ barang.qty_barang }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="barangs.length === 0">
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="relative w-20 h-20 mb-4">
                                            <div class="absolute inset-0 bg-violet-100 rounded-full animate-pulse"></div>
                                            <div class="absolute inset-2 bg-white rounded-full flex items-center justify-center shadow-sm">
                                                <svg class="w-8 h-8 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Barang</h3>
                                        <p class="text-sm text-slate-500">Coba kata kunci pencarian lain.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Floating Save Bar (visible when changes exist) -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="translate-y-full opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-full opacity-0"
            >
                <div v-if="hasChanges" class="fixed bottom-6 left-1/2 -translate-x-1/2 lg:left-[calc(260px+50%)] lg:-translate-x-1/2 z-40">
                    <div class="flex items-center gap-4 px-6 py-3 bg-slate-900 text-white rounded-2xl shadow-2xl shadow-slate-900/40 border border-slate-700">
                        <span class="text-sm">
                            <strong class="text-violet-400">{{ localChecked.length }}</strong> barang dipilih
                        </span>
                        <button @click="save" :disabled="saving"
                            class="flex items-center gap-2 px-4 py-1.5 bg-violet-500 hover:bg-violet-400 text-white text-sm font-bold rounded-xl transition-all disabled:opacity-50">
                            <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </AppLayout>
</template>
