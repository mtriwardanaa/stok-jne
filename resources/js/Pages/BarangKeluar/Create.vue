<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    barangList: Array,
    departments: Array,
    groups: Array,
    users: Array,
})

const form = useForm({
    tanggal: new Date().toISOString().slice(0, 16),
    tipe_penerima: '',       // 'internal' or 'mitra'
    id_divisi: '',            // department_id if internal
    id_group: '',             // group_id if mitra
    id_agen: '',              // selected user id
    nama_user_request: '',
    items: [{ id_barang: '', qty_barang: 1 }],
})

// Cascading: filter users based on type + division/group
const filteredUsers = computed(() => {
    if (!form.tipe_penerima) return []
    return props.users.filter(u => {
        if (form.tipe_penerima === 'internal') {
            return u.type === 'internal' && (!form.id_divisi || String(u.department_id) === String(form.id_divisi))
        } else {
            return u.type === 'partner' && (!form.id_group || String(u.group_id) === String(form.id_group))
        }
    })
})

// Reset dependent fields when tipe changes
watch(() => form.tipe_penerima, () => {
    form.id_divisi = ''
    form.id_group = ''
    form.id_agen = ''
})

watch(() => form.id_divisi, () => {
    form.id_agen = ''
})

watch(() => form.id_group, () => {
    form.id_agen = ''
})

// Auto-fill nama from selected user
watch(() => form.id_agen, (val) => {
    if (val) {
        const user = props.users.find(u => String(u.id) === String(val))
        if (user) form.nama_user_request = user.name
    } else {
        form.nama_user_request = ''
    }
})

const addItem = () => {
    form.items.push({ id_barang: '', qty_barang: 1 })
}

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

const submit = () => {
    form.post('/barang-keluar')
}

const getBarangStock = (id) => {
    const barang = props.barangList.find(b => b.id === id)
    return barang?.qty_barang || 0
}
</script>

<template>
    <AppLayout title="Tambah Barang Keluar">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/barang-keluar" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Barang Keluar</h2>
                    <p class="text-sm text-slate-500">Catat pengeluaran barang dari gudang</p>
                </div>
            </div>

            <!-- Form: Split Layout -->
            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                <!-- LEFT: Detail Penerima -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 sticky top-6">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            Detail Penerima
                        </h3>

                        <!-- Tanggal -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal & Waktu</label>
                            <input type="datetime-local" v-model="form.tanggal" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm">
                        </div>

                        <!-- Tipe Penerima -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipe Organisasi</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" @click="form.tipe_penerima = 'internal'"
                                    class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border text-sm font-semibold transition-all"
                                    :class="form.tipe_penerima === 'internal' 
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-500/20' 
                                        : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:bg-slate-50'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    Internal
                                </button>
                                <button type="button" @click="form.tipe_penerima = 'mitra'"
                                    class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border text-sm font-semibold transition-all"
                                    :class="form.tipe_penerima === 'mitra' 
                                        ? 'border-amber-500 bg-amber-50 text-amber-700 ring-2 ring-amber-500/20' 
                                        : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:bg-slate-50'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Mitra
                                </button>
                            </div>
                        </div>

                        <!-- Divisi (if Internal) -->
                        <div v-if="form.tipe_penerima === 'internal'">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Divisi</label>
                            <SearchableSelect
                                v-model="form.id_divisi"
                                :options="departments.map(d => ({ value: d.id, label: d.name }))"
                                placeholder="Pilih divisi..."
                            />
                        </div>

                        <!-- Group (if Mitra) -->
                        <div v-if="form.tipe_penerima === 'mitra'">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Group</label>
                            <SearchableSelect
                                v-model="form.id_group"
                                :options="groups.map(g => ({ value: g.id, label: g.name }))"
                                placeholder="Pilih group..."
                            />
                        </div>

                        <!-- User -->
                        <div v-if="form.tipe_penerima && (form.id_divisi || form.id_group)">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Penerima</label>
                            <SearchableSelect
                                v-model="form.id_agen"
                                :options="filteredUsers.map(u => ({ value: u.id, label: u.name }))"
                                placeholder="Pilih penerima..."
                            />
                        </div>

                        <!-- Selected user preview -->
                        <div v-if="form.id_agen" class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ form.nama_user_request?.substring(0, 2).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ form.nama_user_request }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ form.tipe_penerima === 'internal' ? 'Karyawan Internal' : 'Mitra' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Detail Barang -->
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    Detail Barang
                                </h3>
                                <button type="button" @click="addItem" class="text-sm text-rose-600 hover:text-rose-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Tambah Item
                                </button>
                            </div>

                            <div class="border border-slate-200 rounded-xl">
                                <!-- Table Header -->
                                <div class="grid grid-cols-12 gap-3 px-4 py-2.5 bg-slate-50 border-b border-slate-200 rounded-t-xl">
                                    <div class="col-span-1 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">#</div>
                                    <div class="col-span-7 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Barang</div>
                                    <div class="col-span-3 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Qty</div>
                                    <div class="col-span-1"></div>
                                </div>

                                <!-- Item Rows -->
                                <div v-for="(item, index) in form.items" :key="index"
                                    class="grid grid-cols-12 gap-3 px-4 py-2.5 items-center border-b border-slate-100 last:border-b-0 hover:bg-slate-50/50 transition-colors"
                                    :style="{ position: 'relative', zIndex: form.items.length - index }">
                                    <div class="col-span-1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-slate-100 text-xs font-bold text-slate-400">{{ index + 1 }}</span>
                                    </div>
                                    <div class="col-span-7">
                                        <SearchableSelect
                                            v-model="item.id_barang"
                                            :options="barangList.map(b => ({ value: b.id, label: b.nama_barang, sublabel: `Stok: ${b.qty_barang} ${b.satuan?.nama_satuan || ''}` }))"
                                            placeholder="Pilih barang..."
                                            :compact="true"
                                        />
                                    </div>
                                    <div class="col-span-3">
                                        <div class="flex items-center gap-2">
                                            <input type="number" v-model="item.qty_barang" min="1" :max="getBarangStock(item.id_barang)" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                            <span v-if="item.id_barang" class="text-[10px] text-slate-400 whitespace-nowrap">/ {{ getBarangStock(item.id_barang) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-span-1 flex justify-center">
                                        <button type="button" @click="removeItem(index)" v-if="form.items.length > 1" class="p-1 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-5 py-4 bg-slate-50 border-t rounded-b-xl flex justify-end gap-3">
                            <Link href="/barang-keluar" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 text-sm font-medium">Batal</Link>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700 disabled:opacity-50">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
