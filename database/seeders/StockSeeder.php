<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Satuan
        $satuanData = [
            ['nama_satuan' => 'Pcs', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Box', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Rim', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Lusin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Set', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Pak', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Unit', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Botol', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('stok_barang_satuan')->insert($satuanData);

        // Seed Supplier
        $supplierData = [
            ['nama_supplier' => 'PT. Mitra Jaya Stationery', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV. Berkah Office Supply', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'Toko Maju Jaya ATK', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT. Indo Alat Kantor', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV. Sumber Makmur', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('stok_supplier')->insert($supplierData);

        // Seed Barang - all rows must have same columns
        $barangData = [
            ['kode_barang' => 'ATK-001', 'nama_barang' => 'Kertas HVS A4 70gsm', 'id_barang_satuan' => 3, 'qty_barang' => 50, 'harga_barang' => 45000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-002', 'nama_barang' => 'Pulpen Hitam Standard', 'id_barang_satuan' => 6, 'qty_barang' => 20, 'harga_barang' => 35000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-003', 'nama_barang' => 'Spidol Whiteboard', 'id_barang_satuan' => 6, 'qty_barang' => 8, 'harga_barang' => 42000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-004', 'nama_barang' => 'Binder Clips Besar', 'id_barang_satuan' => 2, 'qty_barang' => 3, 'harga_barang' => 25000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-005', 'nama_barang' => 'Stapler HD-50', 'id_barang_satuan' => 1, 'qty_barang' => 15, 'harga_barang' => 85000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-006', 'nama_barang' => 'Isi Staples No.10', 'id_barang_satuan' => 2, 'qty_barang' => 25, 'harga_barang' => 8000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-007', 'nama_barang' => 'Map Plastik Kancing', 'id_barang_satuan' => 4, 'qty_barang' => 12, 'harga_barang' => 36000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'ATK-008', 'nama_barang' => 'Pensil 2B Faber', 'id_barang_satuan' => 4, 'qty_barang' => 5, 'harga_barang' => 30000, 'internal' => true, 'agen' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'UM-001', 'nama_barang' => 'Tinta Printer Epson Black', 'id_barang_satuan' => 8, 'qty_barang' => 6, 'harga_barang' => 75000, 'internal' => false, 'agen' => true, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'UM-002', 'nama_barang' => 'Toner HP 35A', 'id_barang_satuan' => 1, 'qty_barang' => 2, 'harga_barang' => 350000, 'internal' => false, 'agen' => true, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'UM-003', 'nama_barang' => 'Baterai AA Alkaline', 'id_barang_satuan' => 6, 'qty_barang' => 0, 'harga_barang' => 45000, 'internal' => false, 'agen' => true, 'created_at' => now(), 'updated_at' => now()],
            ['kode_barang' => 'UM-004', 'nama_barang' => 'Lampu LED 12W', 'id_barang_satuan' => 1, 'qty_barang' => 4, 'harga_barang' => 25000, 'internal' => false, 'agen' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('stok_barang')->insert($barangData);

        // Seed sample Barang Masuk
        $now = Carbon::now();
        DB::table('stok_barang_masuk')->insert([
            'no_barang_masuk' => 'NBM-' . $now->format('md') . '-001',
            'tanggal' => $now->copy()->subDays(5),
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed FIFO price records for existing stock
        foreach ($barangData as $idx => $barang) {
            if ($barang['qty_barang'] > 0) {
                DB::table('stok_barang_harga')->insert([
                    'id_barang' => $idx + 1,
                    'id_barang_masuk' => 1,
                    'qty_barang' => $barang['qty_barang'],
                    'harga_barang' => $barang['harga_barang'],
                    'tanggal_barang' => $now->copy()->subDays(5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed sample Order (Pending) - using 'menunggu' status
        DB::table('stok_order')->insert([
            [
                'no_order' => 'ORD-' . $now->format('ymd') . '-001',
                'tanggal' => $now->copy()->subDays(2),
                'id_divisi' => 1,
                'nama_user_request' => 'Ahmad Rizki',
                'status' => 'menunggu',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_order' => 'ORD-' . $now->format('ymd') . '-002',
                'tanggal' => $now->copy()->subDay(),
                'id_divisi' => 2,
                'nama_user_request' => 'Siti Nurhaliza',
                'status' => 'menunggu',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seed Order Details
        DB::table('stok_order_detail')->insert([
            ['id_order' => 1, 'id_barang' => 1, 'qty_barang' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id_order' => 1, 'id_barang' => 2, 'qty_barang' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_order' => 2, 'id_barang' => 5, 'qty_barang' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_order' => 2, 'id_barang' => 9, 'qty_barang' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
