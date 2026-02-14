<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    order: Object,
    approvedQty: Object,
    orderHistory: Array,
})

const showApproveModal = ref(false)
const showRejectModal = ref(false)
const showHistoryModal = ref(false)
const historyOrder = ref(null)

const approvedQtyLocal = ref({ ...props.approvedQty })

const rejectForm = useForm({
    rejectReason: '',
})

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })
}

const getStatusColor = (s) => {
    const colors = {
        menunggu: 'bg-amber-100 text-amber-800 border-amber-200',
        diproses: 'bg-blue-100 text-blue-800 border-blue-200',
        selesai: 'bg-emerald-100 text-emerald-800 border-emerald-200',
        ditolak: 'bg-rose-100 text-rose-800 border-rose-200',
    }
    return colors[s] || 'bg-slate-100 text-slate-800'
}

const isQtyOverStock = (detail) => {
    const qty = approvedQtyLocal.value[detail.id] || 0
    const stock = detail.barang?.qty_barang || 0
    return qty > stock
}

const hasStockError = computed(() => {
    return props.order.details?.some(d => isQtyOverStock(d)) || false
})

const approve = () => {
    if (hasStockError.value) return
    useForm({ approvedQty: approvedQtyLocal.value }).post(`/order/${props.order.id}/approve`)
}

const reject = () => {
    rejectForm.post(`/order/${props.order.id}/reject`)
}

const openHistoryDetail = (order) => {
    historyOrder.value = order
    showHistoryModal.value = true
}
</script>

<template>
    <AppLayout title="Detail Order">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="/order" class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ order.no_order }}</h2>
                        <p class="text-sm text-slate-500">{{ formatDate(order.tanggal) }}</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase border" :class="getStatusColor(order.status)">
                    {{ order.status }}
                </span>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">No. Order</p>
                        <p class="font-semibold text-slate-800">{{ order.no_order }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Tanggal</p>
                        <p class="font-semibold text-slate-800">{{ formatDate(order.tanggal) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Pemohon</p>
                        <p class="font-semibold text-slate-800">{{ order.created_user?.name || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase mb-1">Dept / Group</p>
                        <p v-if="order.created_user?.department" class="font-semibold text-slate-800">{{ order.created_user?.department.name }}</p>
                        <p v-else-if="order.created_user?.group" class="font-semibold text-slate-800">{{ order.created_user?.group.name }}</p>
                        <p v-else class="text-xs text-slate-400">-</p>
                    </div>
                </div>

                <!-- Approve/Reject Info -->
                <div v-if="order.status === 'selesai'" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-emerald-600 font-medium uppercase mb-1">Diapprove oleh</p>
                        <p class="font-semibold text-slate-800">{{ order.approved_user?.name || '-' }}</p>
                        <p v-if="order.tanggal_approve" class="text-xs text-slate-400 mt-0.5">{{ formatDate(order.tanggal_approve) }}</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Detail Item</h3>
                    <span class="text-sm text-slate-500">{{ order.details?.length || 0 }} item</span>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-indigo-100 border-b border-indigo-200">
                        <tr class="text-[11px] font-semibold text-indigo-800 uppercase">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3 text-center">Qty Diminta</th>
                            <th class="px-6 py-3 text-center">Stok Tersedia</th>
                            <th v-if="order.status === 'menunggu'" class="px-6 py-3 text-center">Qty Approve</th>
                            <th v-if="order.status === 'selesai'" class="px-6 py-3 text-center">Qty Diberikan</th>
                            <th class="px-6 py-3">Satuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(detail, index) in order.details" :key="detail.id">
                            <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ detail.barang?.nama_barang }}</p>
                                <p class="text-xs text-slate-500">{{ detail.barang?.kode_barang }}</p>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">{{ detail.qty_barang }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold" :class="detail.barang?.qty_barang >= detail.qty_barang ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                                    {{ detail.barang?.qty_barang || 0 }}
                                </span>
                            </td>
                            <td v-if="order.status === 'menunggu'" class="px-6 py-4 text-center">
                                <div>
                                    <input type="number" v-model="approvedQtyLocal[detail.id]" :max="Math.min(detail.qty_barang, detail.barang?.qty_barang || 0)" min="0" 
                                        class="w-20 px-2 py-1 text-center border rounded-lg text-sm transition-colors"
                                        :class="isQtyOverStock(detail) 
                                            ? 'border-rose-400 bg-rose-50 text-rose-700' 
                                            : 'border-slate-200'">
                                    <p v-if="isQtyOverStock(detail)" class="text-[10px] text-rose-500 font-medium mt-1">⚠ Melebihi stok!</p>
                                </div>
                            </td>
                            <td v-if="order.status === 'selesai'" class="px-6 py-4 text-center font-semibold text-emerald-600">
                                {{ detail.qty_approved || 0 }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ detail.barang?.satuan?.nama_satuan || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Actions for pending orders -->
            <div v-if="order.status === 'menunggu'" class="flex justify-end gap-3">
                <button @click="showRejectModal = true" class="px-6 py-2.5 border border-rose-200 text-rose-600 rounded-xl font-semibold hover:bg-rose-50 transition-colors">
                    Tolak Order
                </button>
                <button @click="showApproveModal = true" :disabled="hasStockError" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Approve Order
                </button>
            </div>

            <!-- Rejection info -->
            <div v-if="order.status === 'ditolak'" class="bg-rose-50 border border-rose-200 rounded-xl p-6">
                <h4 class="font-bold text-rose-800 mb-2">Order Ditolak</h4>
                <p class="text-sm text-rose-700 mb-2">{{ order.rejected_text }}</p>
                <p class="text-xs text-rose-500">Ditolak oleh {{ order.rejected_user?.name }} pada {{ formatDate(order.tanggal_reject) }}</p>
            </div>

            <!-- Order History -->
            <div v-if="orderHistory.length > 0" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-amber-50">
                    <h3 class="font-bold text-slate-800">Riwayat Order dari Unit yang Sama (Bulan Ini)</h3>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button v-for="h in orderHistory" :key="h.id" @click="openHistoryDetail(h)" 
                        class="text-left p-4 rounded-lg border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/50 transition-all">
                        <p class="font-semibold text-slate-800">{{ h.no_order }}</p>
                        <p class="text-xs text-slate-500">{{ formatDate(h.tanggal) }} • {{ h.details?.length || 0 }} item</p>
                        <span class="mt-2 inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase" :class="getStatusColor(h.status)">
                            {{ h.status }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div v-if="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-slate-900/60" @click="showApproveModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Konfirmasi Approve</h3>
                    <p class="text-sm text-slate-600 mb-6">Yakin ingin approve order ini? Barang akan diproses keluar sesuai qty yang disetujui.</p>
                    <div class="flex justify-end gap-3">
                        <button @click="showApproveModal = false" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600">Batal</button>
                        <button @click="approve" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold">Approve</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-slate-900/60" @click="showRejectModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Tolak Order</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan</label>
                        <textarea v-model="rejectForm.rejectReason" rows="4" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="Tuliskan alasan..."></textarea>
                        <p v-if="rejectForm.errors.rejectReason" class="text-rose-500 text-xs mt-1">{{ rejectForm.errors.rejectReason }}</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="showRejectModal = false" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600">Batal</button>
                        <button @click="reject" class="px-4 py-2 bg-rose-600 text-white rounded-lg font-semibold">Tolak</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div v-if="showHistoryModal && historyOrder" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-slate-900/60" @click="showHistoryModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">{{ historyOrder.no_order }}</h3>
                    <table class="w-full text-sm">
                        <thead class="bg-indigo-100 border-b border-indigo-200">
                            <tr><th class="px-3 py-2 text-left">Barang</th><th class="px-3 py-2 text-center">Qty</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="d in historyOrder.details" :key="d.id">
                                <td class="px-3 py-2">{{ d.barang?.nama_barang }}</td>
                                <td class="px-3 py-2 text-center">{{ d.qty_barang }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4 flex justify-end">
                        <button @click="showHistoryModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
