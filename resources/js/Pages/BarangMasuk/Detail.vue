<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    barangMasuk: Object,
})

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(value)

const totalNilai = () => {
    return props.barangMasuk.details?.reduce((sum, d) => sum + (d.qty_barang * d.harga_barang), 0) || 0
}
</script>

<template>
    <AppLayout title="Detail Barang Masuk">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/barang-masuk" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </Link>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ barangMasuk.no_barang_masuk }}</h2>
                        <p class="text-sm text-slate-500">{{ formatDate(barangMasuk.tanggal) }}</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold uppercase">Barang Masuk</span>
            </div>

            <!-- Info Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">No. Barang Masuk</p>
                        <p class="font-semibold text-slate-800">{{ barangMasuk.no_barang_masuk }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Tanggal</p>
                        <p class="font-semibold text-slate-800">{{ formatDate(barangMasuk.tanggal) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Dibuat oleh</p>
                        <p class="font-semibold text-slate-800">{{ barangMasuk.created_user?.name || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Detail Item</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-xs font-bold text-slate-500 uppercase">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                            <th class="px-6 py-3 text-right">Harga</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(detail, index) in barangMasuk.details" :key="detail.id">
                            <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ detail.barang?.nama_barang }}</p>
                                <p class="text-xs text-slate-500">{{ detail.barang?.kode_barang }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ detail.supplier?.nama_supplier || '-' }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ detail.qty_barang }} {{ detail.barang?.satuan?.nama_satuan }}</td>
                            <td class="px-6 py-4 text-right text-slate-600">Rp {{ formatCurrency(detail.harga_barang) }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-800">Rp {{ formatCurrency(detail.qty_barang * detail.harga_barang) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-emerald-50 border-t-2 border-emerald-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right font-bold text-emerald-700">Total Nilai</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700 text-lg">Rp {{ formatCurrency(totalNilai()) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
