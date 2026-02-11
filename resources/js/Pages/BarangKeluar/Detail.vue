<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    barangKeluar: Object,
})

const invoice = computed(() => props.barangKeluar.invoice)
const hasInvoice = computed(() => !!invoice.value)

// Editable invoice items
const invoiceItems = ref([])
const isEditing = ref(false)
const saving = ref(false)

// Initialize invoice items when invoice exists
const initInvoiceItems = () => {
    if (invoice.value?.details) {
        invoiceItems.value = invoice.value.details.map(d => ({
            id: d.id,
            nama_barang: d.barang?.nama_barang || '',
            kode_barang: d.barang?.kode_barang || '',
            satuan: d.barang?.satuan?.nama_satuan || '-',
            qty: d.qty,
            harga: parseFloat(d.harga),
        }))
    }
}

// Watch for invoice data
if (hasInvoice.value) {
    initInvoiceItems()
}

const generateInvoice = () => {
    if (confirm('Generate invoice dari barang keluar ini?')) {
        router.post(`/barang-keluar/${props.barangKeluar.id}/generate-invoice`, {}, {
            preserveScroll: true,
        })
    }
}

const startEditing = () => {
    initInvoiceItems()
    isEditing.value = true
}

const cancelEditing = () => {
    initInvoiceItems()
    isEditing.value = false
}

const saveInvoice = () => {
    saving.value = true
    router.put(`/invoice/${invoice.value.id}`, {
        items: invoiceItems.value.map(item => ({
            id: item.id,
            harga: item.harga,
        })),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false
            saving.value = false
        },
        onError: () => {
            saving.value = false
        },
    })
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const formatCurrency = (val) => {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID')
}

const getTotalItems = () => {
    return props.barangKeluar.details?.reduce((sum, d) => sum + d.qty_barang, 0) || 0
}

const getInvoiceTotal = () => {
    return invoiceItems.value.reduce((sum, item) => sum + (item.qty * item.harga), 0)
}
</script>

<template>
    <AppLayout title="Detail Barang Keluar">
        <div class="space-y-6">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                {{ $page.props.flash.error }}
            </div>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Link href="/barang-keluar" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </Link>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ barangKeluar.no_barang_keluar }}</h2>
                        <p class="text-sm text-slate-500">{{ formatDate(barangKeluar.tanggal) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <a v-if="hasInvoice" :href="`/barang-keluar/${barangKeluar.id}/invoice`" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Invoice
                    </a>
                    <a :href="`/barang-keluar/${barangKeluar.id}/surat-jalan`" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Surat Jalan
                    </a>
                    <span class="px-3 py-1.5 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold uppercase">Barang Keluar</span>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">No. Barang Keluar</p>
                        <p class="font-semibold text-slate-800">{{ barangKeluar.no_barang_keluar }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Tanggal</p>
                        <p class="font-semibold text-slate-800">{{ formatDate(barangKeluar.tanggal) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Dibuat oleh</p>
                        <p class="font-semibold text-slate-800">{{ barangKeluar.created_user?.name || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Dari Order</p>
                        <Link v-if="barangKeluar.order" :href="`/order/${barangKeluar.order.id}`" class="font-semibold text-indigo-600 hover:text-indigo-700">
                            {{ barangKeluar.order.no_order }}
                        </Link>
                        <p v-else class="text-slate-400">-</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Detail Item</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-indigo-100 border-b border-indigo-200">
                        <tr class="text-[11px] font-semibold text-indigo-800 uppercase">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3">Satuan</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(detail, index) in barangKeluar.details" :key="detail.id">
                            <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ detail.barang?.nama_barang }}</p>
                                <p class="text-xs text-slate-500">{{ detail.barang?.kode_barang }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ detail.barang?.satuan?.nama_satuan || '-' }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-800">{{ detail.qty_barang }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-rose-50 border-t-2 border-rose-200">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-rose-700">Total Qty</td>
                            <td class="px-6 py-4 text-center font-bold text-rose-700 text-lg">{{ getTotalItems() }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Invoice Section -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-indigo-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="font-bold text-slate-800">Invoice</h3>
                        <span v-if="hasInvoice" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-lg"
                            :class="invoice.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                            {{ invoice.status }}
                        </span>
                    </div>
                    <div v-if="hasInvoice && !isEditing" class="flex items-center gap-2">
                        <button @click="startEditing"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-100 hover:bg-indigo-200 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Harga
                        </button>
                    </div>
                </div>

                <!-- No invoice yet -->
                <div v-if="!hasInvoice" class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 mb-4">Belum ada invoice untuk barang keluar ini</p>
                    <button @click="generateInvoice"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Generate Invoice
                    </button>
                </div>

                <!-- Invoice exists -->
                <div v-else>
                    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100 text-sm text-slate-600">
                        <span class="font-semibold text-slate-800">{{ invoice.no_invoice }}</span>
                        <span class="mx-2">·</span>
                        {{ formatDate(invoice.tanggal_invoice) }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-indigo-100 border-b border-indigo-200">
                                <tr class="text-[11px] font-semibold text-indigo-800 uppercase">
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">Barang</th>
                                    <th class="px-6 py-3">Satuan</th>
                                    <th class="px-6 py-3 text-center">Qty</th>
                                    <th class="px-6 py-3 text-right">Harga</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="(item, index) in invoiceItems" :key="item.id">
                                    <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-slate-800">{{ item.nama_barang }}</p>
                                        <p class="text-xs text-slate-500">{{ item.kode_barang }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ item.satuan }}</td>
                                    <td class="px-6 py-4 text-center font-semibold text-slate-800">{{ item.qty }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <input v-if="isEditing" type="number" v-model.number="item.harga" min="0" step="100"
                                            class="w-36 px-3 py-1.5 text-right border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                                        <span v-else class="font-medium text-slate-800">{{ formatCurrency(item.harga) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-800">
                                        {{ formatCurrency(item.qty * item.harga) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-indigo-50 border-t-2 border-indigo-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-right font-bold text-indigo-700">TOTAL</td>
                                    <td class="px-6 py-4 text-right font-bold text-indigo-700 text-lg">{{ formatCurrency(getInvoiceTotal()) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Edit Actions -->
                    <div v-if="isEditing" class="px-6 py-4 bg-amber-50 border-t border-amber-200 flex items-center justify-between">
                        <p class="text-sm text-amber-700">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ubah harga sesuai kebutuhan invoice, lalu klik Simpan
                        </p>
                        <div class="flex items-center gap-2">
                            <button @click="cancelEditing" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Batal
                            </button>
                            <button @click="saveInvoice" :disabled="saving"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                {{ saving ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
