<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    suppliers: Object,
    filters: Object,
})

const search = ref(props.filters.search)
const showModal = ref(false)
const editMode = ref(false)

const form = useForm({
    id: null,
    nama_supplier: '',
})

let searchTimeout = null
watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get('/supplier', { search: value }, { preserveState: true, preserveScroll: true })
    }, 300)
})

import { router } from '@inertiajs/vue3'

const openCreateModal = () => {
    form.reset()
    editMode.value = false
    showModal.value = true
}

const openEditModal = (supplier) => {
    form.id = supplier.id
    form.nama_supplier = supplier.nama_supplier
    editMode.value = true
    showModal.value = true
}

const save = () => {
    if (editMode.value) {
        form.put(`/supplier/${form.id}`, {
            onSuccess: () => showModal.value = false,
        })
    } else {
        form.post('/supplier', {
            onSuccess: () => showModal.value = false,
        })
    }
}

const deleteSupplier = (id) => {
    if (confirm('Yakin hapus supplier ini?')) {
        router.delete(`/supplier/${id}`)
    }
}
</script>

<template>
    <AppLayout title="Supplier">
        <div class="space-y-6">
            <!-- Flash -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                {{ $page.props.flash.success }}
            </div>

            <!-- Header -->
            <div class="relative rounded-2xl p-6 bg-white border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-cyan-500/10 rounded-xl">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Supplier</h2>
                            <p class="text-sm text-slate-500">Kelola data supplier barang</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <input type="text" v-model="search" placeholder="Cari supplier..." 
                            class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 w-60">
                        <button @click="openCreateModal" class="px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm font-medium hover:bg-cyan-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Nama Supplier</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(supplier, index) in suppliers.data" :key="supplier.id" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 text-slate-500">{{ suppliers.from + index }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ supplier.nama_supplier }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal(supplier)" class="p-2 text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button @click="deleteSupplier(supplier.id)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="suppliers.data.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500">Tidak ada data supplier</td>
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
                    <h3 class="text-lg font-bold text-slate-800 mb-4">{{ editMode ? 'Edit' : 'Tambah' }} Supplier</h3>
                    <form @submit.prevent="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Supplier</label>
                            <input type="text" v-model="form.nama_supplier" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="PT. Supplier ABC">
                            <p v-if="form.errors.nama_supplier" class="text-rose-500 text-xs mt-1">{{ form.errors.nama_supplier }}</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-cyan-600 text-white rounded-lg font-semibold disabled:opacity-50">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
