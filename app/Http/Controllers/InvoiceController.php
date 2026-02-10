<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Generate invoice from barang keluar
     * Pre-fills prices from barang master data
     */
    public function generate($barangKeluarId)
    {
        $barangKeluar = BarangKeluar::with('details.barang')->findOrFail($barangKeluarId);

        // Check if invoice already exists
        if ($barangKeluar->invoice) {
            return back()->with('error', 'Invoice sudah pernah dibuat untuk barang keluar ini.');
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'no_invoice' => 'INV-' . date('Ymd') . '-' . str_pad($barangKeluar->id, 4, '0', STR_PAD_LEFT),
                'id_barang_keluar' => $barangKeluar->id,
                'tanggal_invoice' => now(),
                'status' => 'unpaid',
                'created_by' => Auth::id(),
            ]);

            foreach ($barangKeluar->details as $detail) {
                InvoiceDetail::create([
                    'id_invoice' => $invoice->id,
                    'id_barang' => $detail->id_barang,
                    'qty' => $detail->qty_barang,
                    'harga' => $detail->barang->harga_barang ?? 0,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Invoice berhasil di-generate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Update invoice detail prices
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::with('details')->findOrFail($id);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stok_invoice_detail,id',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                InvoiceDetail::where('id', $item['id'])
                    ->where('id_invoice', $invoice->id)
                    ->update(['harga' => $item['harga']]);
            }

            // Update status if provided
            if ($request->has('status')) {
                $invoice->update(['status' => $request->status]);
            }

            DB::commit();
            return back()->with('success', 'Invoice berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update invoice: ' . $e->getMessage());
        }
    }
}
