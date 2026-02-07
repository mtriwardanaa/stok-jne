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
            DB::table('stok_supplier')->updateOrInsert(
                ['id' => $s->id],
                [
                    'nama_supplier' => $s->nama_supplier,
                    'created_at' => $s->created_at,
                    'updated_at' => $s->updated_at,
                ]
            );
        }
        $this->info("  -> {$suppliers->count()} suppliers migrated");

        // 2. Migrate Satuan
        $this->info('Migrating stok_barang_satuan...');
        $satuans = DB::connection('laporit')->table('stok_barang_satuan')->get();
        foreach ($satuans as $s) {
            DB::table('stok_barang_satuan')->updateOrInsert(
                ['id' => $s->id],
                [
                    'nama_satuan' => $s->nama_satuan,
                    'created_at' => $s->created_at,
                    'updated_at' => $s->updated_at,
                ]
            );
        }
        $this->info("  -> {$satuans->count()} satuan migrated");

        // 3. Migrate Barang
        $this->info('Migrating stok_barang...');
        $barangs = DB::connection('laporit')->table('stok_barang')->get();
        foreach ($barangs as $b) {
            DB::table('stok_barang')->updateOrInsert(
                ['id' => $b->id],
                [
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
                ]
            );
        }
        $this->info("  -> {$barangs->count()} barang migrated");

        // Verification
        $this->newLine();
        $this->info('VERIFICATION:');
        $this->table(
            ['Table', 'Source (laporit)', 'Target (jne_stok)'],
            [
                ['stok_supplier', $oldSupplier, DB::table('stok_supplier')->count()],
                ['stok_barang_satuan', $oldSatuan, DB::table('stok_barang_satuan')->count()],
                ['stok_barang', $oldBarang, DB::table('stok_barang')->count()],
            ]
        );

        $this->info('Step 2 completed!');
        return Command::SUCCESS;
    }
}
