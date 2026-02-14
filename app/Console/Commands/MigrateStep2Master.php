<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep2Master extends Command
{
    protected $signature = 'migrate:step2-master 
        {--dry-run : Show what would be done without executing}
        {--truncate : Truncate target tables before migration}';
    protected $description = 'Step 2: Migrate master data (supplier, satuan, barang)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $truncate = true;
        
        $this->info('===========================================');
        $this->info('STEP 2: MASTER DATA MIGRATION');
        $this->info('===========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Count source data
        $oldSupplier = DB::connection('laporit')->table('stok_supplier')->count();
        $oldSatuan = DB::connection('laporit')->table('stok_barang_satuan')->count();
        $oldBarang = DB::connection('laporit')->table('stok_barang')->count();

        $this->info("Source data from laporit:");
        $this->table(['Table', 'Count'], [
            ['stok_supplier', $oldSupplier],
            ['stok_barang_satuan', $oldSatuan],
            ['stok_barang', $oldBarang],
        ]);

        if ($dryRun) {
            $this->info('DRY RUN - No data will be migrated');
            return Command::SUCCESS;
        }

        // Truncate if requested
        if ($truncate) {
            $this->warn('Truncating target tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('stok_barang_ketersediaan')->truncate();
            DB::table('stok_barang_harga')->truncate();
            DB::table('stok_barang')->truncate();
            DB::table('stok_barang_satuan')->truncate();
            DB::table('stok_supplier')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info('Tables truncated');
        }

        // 1. Migrate Supplier
        $this->info('Migrating stok_supplier...');
        $suppliers = DB::connection('laporit')->table('stok_supplier')->get();
        foreach ($suppliers as $s) {
            DB::table('stok_supplier')->insert([
                'id' => $s->id,
                'nama_supplier' => $s->nama_supplier,
                'created_at' => $s->created_at,
                'updated_at' => $s->updated_at,
            ]);
        }
        $this->info("  -> {$suppliers->count()} suppliers migrated");

        // 2. Migrate Satuan
        $this->info('Migrating stok_barang_satuan...');
        $satuans = DB::connection('laporit')->table('stok_barang_satuan')->get();
        foreach ($satuans as $s) {
            DB::table('stok_barang_satuan')->insert([
                'id' => $s->id,
                'nama_satuan' => $s->nama_satuan,
                'created_at' => $s->created_at,
                'updated_at' => $s->updated_at,
            ]);
        }
        $this->info("  -> {$satuans->count()} satuan migrated");

        // 3. Migrate Barang
        $this->info('Migrating stok_barang...');
        $barangs = DB::connection('laporit')->table('stok_barang')->get();
        foreach ($barangs as $b) {
            DB::table('stok_barang')->insert([
                'id' => $b->id,
                'kode_barang' => $b->kode_barang,
                'nama_barang' => $b->nama_barang,
                'qty_barang' => $b->qty_barang ?? 0,
                'harga_barang' => $b->harga_barang ?? 0,
                'warning_stok' => $b->warning_stok ?? 0,
                'stok_awal' => $b->stok_awal ?? 0,
                'id_barang_satuan' => $b->id_barang_satuan,
                'internal' => $b->internal ?? 0,
                'agen' => $b->agen ?? 0,
                'subagen' => $b->subagen ?? 0,
                'corporate' => $b->corporate ?? 0,
                'created_at' => $b->created_at,
                'updated_at' => $b->updated_at,
            ]);
        }
        $this->info("  -> {$barangs->count()} barang migrated");

        // 4. Migrate Ketersediaan (from boolean columns to pivot table)
        $this->info('Migrating stok_barang_ketersediaan...');
        $ketersediaanCount = 0;
        // Partner mapping: agen => partner_id 1, subagen => 2, corporate => 3, kantor perwakilan => 4 (sama dengan subagen)
        $partnerMapping = [
            'agen' => 1,      // AGEN HYBRID
            'subagen' => 2,   // CABANG / SUB AGEN
            'corporate' => 3, // CUSTOMER CORPORATE
        ];
        // KANTOR PERWAKILAN (4) ikut ketersediaan yang sama dengan subagen
        $kpPartnerId = 4;
        foreach ($barangs as $b) {
            // Internal
            if (!empty($b->internal)) {
                DB::table('stok_barang_ketersediaan')->insert([
                    'id_barang' => $b->id,
                    'tipe' => 'internal',
                    'partner_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $ketersediaanCount++;
            }
            // Partners
            foreach ($partnerMapping as $col => $partnerId) {
                if (!empty($b->$col)) {
                    DB::table('stok_barang_ketersediaan')->insert([
                        'id_barang' => $b->id,
                        'tipe' => 'partner',
                        'partner_id' => $partnerId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $ketersediaanCount++;
                }
            }
            // KANTOR PERWAKILAN ikut subagen
            if (!empty($b->subagen)) {
                DB::table('stok_barang_ketersediaan')->insert([
                    'id_barang' => $b->id,
                    'tipe' => 'partner',
                    'partner_id' => $kpPartnerId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $ketersediaanCount++;
            }
        }
        $this->info("  -> {$ketersediaanCount} ketersediaan records migrated");

        // Verification
        $this->newLine();
        $this->info('VERIFICATION:');
        $this->table(
            ['Table', 'Source (laporit)', 'Target (jne_stok)'],
            [
                ['stok_supplier', $oldSupplier, DB::table('stok_supplier')->count()],
                ['stok_barang_satuan', $oldSatuan, DB::table('stok_barang_satuan')->count()],
                ['stok_barang', $oldBarang, DB::table('stok_barang')->count()],
                ['stok_barang_ketersediaan', '-', DB::table('stok_barang_ketersediaan')->count()],
            ]
        );

        $this->info('Step 2 completed!');
        return Command::SUCCESS;
    }
}
