<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    barangList: Array,
    supplierList: Array,
})

const user = usePage().props.auth?.user

const form = useForm({
    tanggal: new Date().toISOString().slice(0, 16),
    items: [{ id_barang: '', id_supplier: '', qty_barang: 1, harga_barang: 0 }],
})

const addItem = () => {
    form.items.push({ id_barang: '', id_supplier: '', qty_barang: 1, harga_barang: 0 })
}

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

// Auto-fill harga from master barang when barang is selected
const onBarangChange = (index, value) => {
    form.items[index].id_barang = value
    if (value) {
        const barang = props.barangList.find(b => String(b.id) === String(value))
        if (barang && barang.harga_barang) {
            form.items[index].harga_barang = Number(barang.harga_barang)
        }
    }
}

const submit = () => {
    form.post('/barang-masuk')
}

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(value)

const totalNilai = () => {
    return form.items.reduce((sum, item) => sum + (item.qty_barang * item.harga_barang), 0)
}
</script>

<template>
    <AppLayout title="Tambah Barang Masuk">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a href="/barang-masuk" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Barang Masuk</h2>
                    <p class="text-sm text-slate-500">Catat penerimaan barang baru ke gudang</p>
                </div>
            </div>

            <!-- Form: Split Layout -->
            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                <!-- LEFT: Info -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 sticky top-6">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            Informasi
                        </h3>

                        <!-- Tanggal -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal & Waktu</label>
                            <input type="datetime-local" v-model="form.tanggal" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
                        </div>

                        <!-- User Info (read-only) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Diinput oleh</label>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    {{ user?.name?.substring(0, 2).toUpperCase() || 'U' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ user?.name || '-' }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ user?.department?.name || user?.username || 'Administrator' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                        </div>

                        <!-- Total Summary -->
                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Item</span>
                                <span class="text-sm font-bold text-slate-700">{{ form.items.length }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Total Nilai</span>
                                <span class="text-lg font-extrabold text-emerald-600">Rp {{ formatCurrency(totalNilai()) }}</span>
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
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    Detail Barang
                                </h3>
                                <button type="button" @click="addItem" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Tambah Item
                                </button>
                            </div>

                            <div class="border border-slate-200 rounded-xl">
                                <!-- Table Header -->
                                <div class="grid grid-cols-12 gap-3 px-4 py-2.5 bg-slate-50 border-b border-slate-200 rounded-t-xl">
                                    <div class="col-span-1 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">#</div>
                                    <div class="col-span-4 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Barang</div>
                                    <div class="col-span-3 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Supplier</div>
                                    <div class="col-span-1 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Qty</div>
                                    <div class="col-span-2 text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Harga Satuan</div>
                                    <div class="col-span-1"></div>
                                </div>

                                <!-- Item Rows -->
                                <div v-for="(item, index) in form.items" :key="index"
                                    class="grid grid-cols-12 gap-3 px-4 py-2.5 items-center border-b border-slate-100 last:border-b-0 hover:bg-slate-50/50 transition-colors"
                                    :style="{ position: 'relative', zIndex: form.items.length - index }">
                                    <div class="col-span-1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-slate-100 text-xs font-bold text-slate-400">{{ index + 1 }}</span>
                                    </div>
                                    <div class="col-span-4">
                                        <SearchableSelect
                                            :modelValue="item.id_barang"
                                            @update:modelValue="onBarangChange(index, $event)"
                                            :options="barangList.map(b => ({ value: b.id, label: b.nama_barang, sublabel: b.kode_barang }))"
                                            placeholder="Pilih barang..."
                                            :compact="true"
                                        />
                                    </div>
                                    <div class="col-span-3">
                                        <SearchableSelect
                                            v-model="item.id_supplier"
                                            :options="supplierList.map(s => ({ value: s.id, label: s.nama_supplier }))"
                                            placeholder="Pilih supplier..."
                                            :compact="true"
                                        />
                                    </div>
                                    <div class="col-span-1">
                                        <input type="number" v-model="item.qty_barang" min="1" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" v-model="item.harga_barang" min="0" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
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
                            <a href="/barang-masuk" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 text-sm font-medium">Batal</a>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 disabled:opacity-50">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
