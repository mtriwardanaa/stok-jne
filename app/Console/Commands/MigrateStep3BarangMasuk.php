<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep3BarangMasuk extends Command
{
    protected $signature = 'migrate:step3-barang-masuk 
        {--dry-run : Show what would be done without executing}
        {--truncate : Truncate target tables before migration}
        {--with-user-mapping : Apply user mapping during migration}';
    protected $description = 'Step 3: Migrate barang masuk data';

    protected array $userMapping = []; // old_id => new_id

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $truncate = true;
        $withUserMapping = true;
        
        $this->info('===========================================');
        $this->info('STEP 3: BARANG MASUK MIGRATION');
        $this->info('===========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        if ($withUserMapping) {
            $this->buildUserMapping();
        }

        // Count source data
        $oldMasuk = DB::connection('laporit')->table('stok_barang_masuk')->count();
        $oldMasukDetail = DB::connection('laporit')->table('stok_barang_masuk_detail')->count();

        $this->info("Source data from laporit:");
        $this->table(['Table', 'Count'], [
            ['stok_barang_masuk', $oldMasuk],
            ['stok_barang_masuk_detail', $oldMasukDetail],
        ]);

        if ($dryRun) {
            $this->showSampleData();
            return Command::SUCCESS;
        }

        // Truncate if requested
        if ($truncate) {
            $this->warn('Truncating target tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('stok_barang_harga')->truncate();
            DB::table('stok_barang_masuk_detail')->truncate();
            DB::table('stok_barang_masuk')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info('Tables truncated');
        }

        // 1. Migrate stok_barang_masuk
        $this->info('Migrating stok_barang_masuk...');
        $masuks = DB::connection('laporit')->table('stok_barang_masuk')->get();
        
        $inserted = 0;
        $skipped = 0;
        $failedMasuk = []; // Track failed records
        
        foreach ($masuks as $m) {
            $createdBy = $this->mapUserId($m->created_by);
            $updatedBy = $this->mapUserId($m->updated_by);
            
            try {
                // Insert with original ID to preserve foreign key relationships
                DB::table('stok_barang_masuk')->insert([
                    'id' => $m->id,
                    'no_barang_masuk' => $m->no_barang_masuk,
                    'tanggal' => $m->tanggal,
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'created_at' => $m->created_at,
                    'updated_at' => $m->updated_at,
                ]);
                
                $inserted++;
            } catch (\Exception $e) {
                $skipped++;
                $failedMasuk[] = [
                    'id' => $m->id,
                    'no_barang_masuk' => $m->no_barang_masuk,
                    'error' => $e->getMessage(),
                ];
            }
        }
        $this->info("  -> {$inserted} barang masuk migrated, {$skipped} skipped");
        
        // Show failed records if any
        if (count($failedMasuk) > 0) {
            $this->warn('  FAILED BARANG MASUK RECORDS:');
            foreach ($failedMasuk as $f) {
                $this->error("    ID: {$f['id']}, No: {$f['no_barang_masuk']}, Error: {$f['error']}");
            }
        }

        // 2. Migrate stok_barang_masuk_detail
        $this->info('Migrating stok_barang_masuk_detail...');
        $details = DB::connection('laporit')->table('stok_barang_masuk_detail')->get();
        
        $insertedDetail = 0;
        $skippedDetail = 0;
        $failedDetail = []; // Track failed records
        foreach ($details as $d) {
            try {
                DB::table('stok_barang_masuk_detail')->insert([
                    'id' => $d->id,
                    'id_barang_masuk' => $d->id_barang_masuk,
                    'id_barang' => $d->id_barang,
                    'id_supplier' => $d->id_supplier ?? 1,
                    'qty_barang' => $d->qty_barang ?? 0,
                    'harga_barang' => $d->harga_barang ?? 0,
                    'created_at' => $d->created_at,
                    'updated_at' => $d->updated_at,
                ]);
                $insertedDetail++;
            } catch (\Exception $e) {
                $skippedDetail++;
                $failedDetail[] = [
                    'id' => $d->id ?? 'N/A',
                    'id_barang_masuk' => $d->id_barang_masuk,
                    'id_barang' => $d->id_barang,
                    'error' => $e->getMessage(),
                ];
            }
        }
        $this->info("  -> {$insertedDetail} detail migrated, {$skippedDetail} skipped");
        
        // Show failed records if any
        // if (count($failedDetail) > 0) {
        //     $this->warn('  FAILED DETAIL RECORDS:');
        //     foreach ($failedDetail as $f) {
        //         $this->error("    ID Masuk: {$f['id_barang_masuk']}, ID Barang: {$f['id_barang']}, Error: {$f['error']}");
        //     }
        // }

        
        
        // Show failed records if any
        // if (count($failedHarga) > 0) {
        //     $this->warn('  FAILED HARGA RECORDS:');
        //     foreach ($failedHarga as $f) {
        //         $this->error("    ID Masuk: {$f['id_barang_masuk']}, ID Barang: {$f['id_barang']}, Error: {$f['error']}");
        //     }
        // }

        // Verification
        $this->newLine();
        $this->info('VERIFICATION:');
        $this->table(
            ['Table', 'Source (laporit)', 'Target (jne_stok)'],
            [
                ['stok_barang_masuk', $oldMasuk, DB::table('stok_barang_masuk')->count()],
                ['stok_barang_masuk_detail', $oldMasukDetail, DB::table('stok_barang_masuk_detail')->count()],
            ]
        );

        $this->info('Step 3 completed!');
        return Command::SUCCESS;
    }

    private function buildUserMapping(): void
    {
        $this->info('Building user mapping...');
        
        $oldUsers = DB::connection('laporit')
            ->table('users')
            ->select('id', 'username')
            ->get();

        $ssoUsers = DB::connection('sso_mysql')
            ->table('users')
            ->get()
            ->keyBy('username');

        foreach ($oldUsers as $old) {
            $ssoUser = $ssoUsers->get($old->username);
            if ($ssoUser) {
                $this->userMapping[$old->id] = $ssoUser->id;
            }
        }
        
        $this->info("  -> {$oldUsers->count()} old users, " . count($this->userMapping) . " mapped");
    }

    private function mapUserId($oldId): ?int
    {
        if ($oldId === null) {
            return null;
        }
        return $this->userMapping[$oldId] ?? $oldId;
    }

    private function showSampleData(): void
    {
        $this->newLine();
        $this->info('SAMPLE DATA (first 5 rows):');
        
        $samples = DB::connection('laporit')
            ->table('stok_barang_masuk')
            ->select('id', 'no_barang_masuk', 'tanggal', 'created_by', 'updated_by')
            ->limit(5)
            ->get();

        $rows = [];
        foreach ($samples as $s) {
            $mappedCreatedBy = $this->mapUserId($s->created_by);
            $rows[] = [
                $s->id,
                $s->no_barang_masuk,
                $s->tanggal,
                $s->created_by . ($mappedCreatedBy != $s->created_by ? " -> {$mappedCreatedBy}" : ''),
                $s->updated_by,
            ];
        }
        $this->table(['ID', 'No Barang Masuk', 'Tanggal', 'Created By', 'Updated By'], $rows);
    }
}
