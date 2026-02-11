<script setup>
import { ref, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    barangList: Array,
    supplierList: Array,
})

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
                <Link href="/barang-masuk" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Barang Masuk</h2>
                    <p class="text-sm text-slate-500">Catat penerimaan barang baru ke gudang</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="p-6 space-y-6">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal & Waktu</label>
                        <input type="datetime-local" v-model="form.tanggal" class="w-full max-w-xs px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
                    </div>

                    <!-- Items -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-semibold text-slate-700">Detail Barang</label>
                            <button type="button" @click="addItem" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah Item
                            </button>
                        </div>

                        <div class="border border-slate-200 rounded-xl">
                            <!-- Table Header -->
                            <div class="grid grid-cols-12 gap-3 px-4 py-2.5 bg-slate-50 border-b border-slate-200 rounded-t-xl">
                                <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider">#</div>
                                <div class="col-span-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Barang</div>
                                <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Supplier</div>
                                <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Qty</div>
                                <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Harga Satuan</div>
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

                    <!-- Total -->
                    <div class="flex justify-end bg-emerald-50 -mx-6 -mb-6 px-6 py-4 border-t border-emerald-100">
                        <div class="text-right">
                            <p class="text-xs text-emerald-600 font-medium uppercase">Total Nilai</p>
                            <p class="text-2xl font-bold text-emerald-700">Rp {{ formatCurrency(totalNilai()) }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                    <Link href="/barang-masuk" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 text-sm font-medium">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 disabled:opacity-50">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
