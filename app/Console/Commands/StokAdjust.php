<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StokAdjust extends Command
{
    protected $signature = 'stok:adjust 
        {--auto : Automatically adjust all negative stock}
        {--barang= : Adjust specific barang by ID}
        {--qty= : Quantity to adjust (for specific barang)}';
    protected $description = 'Adjust stock by creating barang masuk entries for discrepancies';

    public function handle()
    {
        $auto = $this->option('auto');
        $barangId = $this->option('barang');
        $qty = $this->option('qty');
        
        $this->info('===========================================');
        $this->info('STOCK ADJUSTMENT');
        $this->info('===========================================');

        // Specific barang adjustment
        if ($barangId && $qty) {
            $this->adjustBarang($barangId, (int)$qty);
            return Command::SUCCESS;
        }

        // Find items with negative stock
        $negativeItems = $this->findNegativeStock();

        if ($negativeItems->isEmpty()) {
            $this->info('Tidak ada barang dengan saldo minus. Stok sudah seimbang!');
            return Command::SUCCESS;
        }

        $this->warn("Ditemukan {$negativeItems->count()} barang dengan saldo minus:");
        
        $rows = [];
        foreach ($negativeItems as $item) {
            $rows[] = [
                $item->id_barang,
                $item->kode_barang ?? '-',
                substr($item->nama_barang, 0, 25),
                number_format($item->total_masuk),
                number_format($item->total_keluar),
                number_format($item->saldo),
                abs($item->saldo), // qty needed to fix
            ];
        }
        
        $this->table(
            ['ID', 'Kode', 'Nama', 'Masuk', 'Keluar', 'Saldo', 'Perlu Ditambah'],
            $rows
        );

        if ($auto) {
            if (!$this->confirm('Buat entri adjustment untuk semua barang di atas?', true)) {
                return Command::SUCCESS;
            }
            
            $this->adjustAllNegative($negativeItems);
        } else {
            $this->info('');
            $this->info('Untuk menyesuaikan otomatis, jalankan:');
            $this->info('  php artisan stok:adjust --auto');
            $this->info('');
            $this->info('Untuk menyesuaikan barang tertentu:');
            $this->info('  php artisan stok:adjust --barang=ID --qty=JUMLAH');
        }

        return Command::SUCCESS;
    }

    private function findNegativeStock()
    {
        // Get barang with calculated stock
        $masuk = DB::table('stok_barang_masuk_detail')
            ->select('id_barang', DB::raw('SUM(qty_barang) as total_masuk'))
            ->groupBy('id_barang');

        $keluar = DB::table('stok_barang_keluar_detail')
            ->select('id_barang', DB::raw('SUM(qty_barang) as total_keluar'))
            ->groupBy('id_barang');

        return DB::table('stok_barang as b')
            ->leftJoinSub($masuk, 'm', 'b.id', '=', 'm.id_barang')
            ->leftJoinSub($keluar, 'k', 'b.id', '=', 'k.id_barang')
            ->select(
                'b.id as id_barang',
                'b.kode_barang',
                'b.nama_barang',
                DB::raw('COALESCE(m.total_masuk, 0) as total_masuk'),
                DB::raw('COALESCE(k.total_keluar, 0) as total_keluar'),
                DB::raw('COALESCE(m.total_masuk, 0) - COALESCE(k.total_keluar, 0) as saldo')
            )
            ->havingRaw('saldo < 0')
            ->get();
    }

    private function adjustAllNegative($items)
    {
        $this->info('Membuat adjustment entries...');
        
        // Create a single barang masuk record for adjustments
        $barangMasukId = DB::table('stok_barang_masuk')->insertGetId([
            'no_barang_masuk' => 'ADJ-' . date('Ymd-His'),
            'tanggal' => now()->toDateString(),
            'created_by' => 1, // System/Admin
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Created barang masuk: ADJ-" . date('Ymd-His') . " (ID: {$barangMasukId})");

        foreach ($items as $item) {
            $qtyAdjust = abs($item->saldo);
            
            // Create detail entry
            DB::table('stok_barang_masuk_detail')->insert([
                'id_barang_masuk' => $barangMasukId,
                'id_barang' => $item->id_barang,
                'id_supplier' => 1, // Default supplier
                'qty_barang' => $qtyAdjust,
                'harga_barang' => 0, // Adjustment, no cost
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("  -> {$item->nama_barang}: +{$qtyAdjust}");
        }

        $this->info('');
        $this->info('Adjustment selesai! Jalankan stok:verify untuk memastikan.');
    }

    private function adjustBarang($barangId, $qty)
    {
        $barang = DB::table('stok_barang')->where('id', $barangId)->first();
        
        if (!$barang) {
            $this->error("Barang dengan ID {$barangId} tidak ditemukan!");
            return;
        }

        $this->info("Barang: {$barang->nama_barang}");
        $this->info("Quantity: +{$qty}");

        if (!$this->confirm('Buat adjustment entry?', true)) {
            return;
        }

        // Create barang masuk
        $barangMasukId = DB::table('stok_barang_masuk')->insertGetId([
            'no_barang_masuk' => 'ADJ-' . date('Ymd-His') . '-' . $barangId,
            'tanggal' => now()->toDateString(),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create detail
        DB::table('stok_barang_masuk_detail')->insert([
            'id_barang_masuk' => $barangMasukId,
            'id_barang' => $barangId,
            'id_supplier' => 1,
            'qty_barang' => $qty,
            'harga_barang' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Adjustment created! No: ADJ-" . date('Ymd-His') . "-{$barangId}");
    }
}
