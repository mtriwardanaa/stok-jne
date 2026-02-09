<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep4BarangKeluar extends Command
{
    protected $signature = 'migrate:step4-barang-keluar 
        {--dry-run : Show what would be done without executing}
        {--truncate : Truncate target tables before migration}
        {--with-user-mapping : Apply user mapping during migration}';
    protected $description = 'Step 4: Migrate barang keluar & order data';

    protected array $userMapping = []; // old_id => new_id

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $truncate = true;
        $withUserMapping = true;
        
        $this->info('===========================================');
        $this->info('STEP 4: BARANG KELUAR & ORDER MIGRATION');
        $this->info('===========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        if ($withUserMapping) {
            $this->buildUserMapping();
        }

        // Count source data
        $oldOrder = DB::connection('laporit')->table('stok_order')->count();
        $oldOrderDetail = DB::connection('laporit')->table('stok_order_detail')->count();
        $oldKeluar = DB::connection('laporit')->table('stok_barang_keluar')->count();
        $oldKeluarDetail = DB::connection('laporit')->table('stok_barang_keluar_detail')->count();
        $oldHarga = DB::connection('laporit')->table('stok_barang_harga')->count();

        $this->info("Source data from laporit:");
        $this->table(['Table', 'Count'], [
            ['stok_order', $oldOrder],
            ['stok_order_detail', $oldOrderDetail],
            ['stok_barang_keluar', $oldKeluar],
            ['stok_barang_keluar_detail', $oldKeluarDetail],
            ['stok_barang_harga', $oldHarga],
        ]);

        if ($dryRun) {
            $this->showSampleOrderData();
            return Command::SUCCESS;
        }

        // Truncate if requested
        if ($truncate) {
            $this->warn('Truncating target tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('stok_barang_keluar_detail')->truncate();
            DB::table('stok_barang_keluar')->truncate();
            DB::table('stok_order_detail')->truncate();
            DB::table('stok_order')->truncate();
            DB::table('stok_barang_harga')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info('Tables truncated');
        }

        // 1. Migrate stok_order
        $this->info('Migrating stok_order...');
        $orders = DB::connection('laporit')->table('stok_order')->get();
        
        $insertedOrder = 0;
        $skippedOrder = 0;
        foreach ($orders as $o) {
            $createdBy = $this->mapUserId($o->created_by);
            $updatedBy = $this->mapUserId($o->updated_by);
            $approvedBy = $this->mapUserId($o->approved_by);
            $rejectedBy = $this->mapUserId($o->rejected_by);

            // Determine status
            $status = 'menunggu';
            if ($o->tanggal_reject !== null) {
                $status = 'ditolak';
            } elseif ($o->tanggal_approve !== null) {
                $status = 'selesai';
            } elseif ($o->tanggal_update !== null) {
                $status = 'diproses';
            }
            
            try {
                DB::table('stok_order')->insert([
                    'id' => $o->id,
                    'no_order' => $o->no_order,
                    'tanggal' => $o->tanggal,
                    'tanggal_update' => $o->tanggal_update,
                    'tanggal_approve' => $o->tanggal_approve,
                    'tanggal_reject' => $o->tanggal_reject,
                    'id_divisi' => $o->id_divisi,
                    'id_kategori' => $o->id_kategori,
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'approved_by' => $approvedBy,
                    'rejected_by' => $rejectedBy,
                    'rejected_text' => $o->rejected_text ?? null,
                    'nama_user_request' => $o->nama_user_request,
                    'hp_user_request' => $o->hp_user_request,
                    'status' => $status,
                    'created_at' => $o->created_at,
                    'updated_at' => $o->updated_at,
                ]);
                $insertedOrder++;
            } catch (\Exception $e) {
                $skippedOrder++;
            }
        }
        $this->info("  -> {$insertedOrder} orders migrated, {$skippedOrder} skipped");

        // 2. Migrate stok_order_detail
        $this->info('Migrating stok_order_detail...');
        $orderDetails = DB::connection('laporit')->table('stok_order_detail')->get();
        
        $insertedOD = 0;
        $skippedOD = 0;
        foreach ($orderDetails as $d) {
            try {
                DB::table('stok_order_detail')->insert([
                    'id' => $d->id,
                    'id_order' => $d->id_order,
                    'id_barang' => $d->id_barang,
                    'qty_barang' => $d->qty_barang ?? 0,
                    'qty_approved' => $d->jumlah_approve ?? 0,
                    'created_at' => $d->created_at,
                    'updated_at' => $d->updated_at,
                ]);
                $insertedOD++;
            } catch (\Exception $e) {
                $skippedOD++;
            }
        }
        $this->info("  -> {$insertedOD} order details migrated, {$skippedOD} skipped");

        // 3. Migrate stok_barang_keluar
        $this->info('Migrating stok_barang_keluar...');
        $keluars = DB::connection('laporit')->table('stok_barang_keluar')->get();
        
        $insertedKeluar = 0;
        $skippedKeluar = 0;
        foreach ($keluars as $k) {
            $createdBy = $this->mapUserId($k->created_by);
            $updatedBy = $this->mapUserId($k->updated_by);
            
            try {
                DB::table('stok_barang_keluar')->insert([
                    'id' => $k->id,
                    'no_barang_keluar' => $k->no_barang_keluar,
                    'tanggal' => $k->tanggal,
                    'id_divisi' => $k->id_divisi,
                    'id_kategori' => $k->id_kategori,
                    'id_agen' => $k->id_agen ?? null,
                    'id_order' => $k->id_order ?? null,
                    'nama_user_request' => $k->nama_user_request,
                    'distribusi_sales' => $k->distribusi_sales == 1 ? 'yes' : null,
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'created_at' => $k->created_at,
                    'updated_at' => $k->updated_at,
                ]);
                $insertedKeluar++;
            } catch (\Exception $e) {
                $skippedKeluar++;
            }
        }
        $this->info("  -> {$insertedKeluar} barang keluar migrated, {$skippedKeluar} skipped");

        // 4. Migrate stok_barang_keluar_detail
        $this->info('Migrating stok_barang_keluar_detail...');
        $keluarDetails = DB::connection('laporit')->table('stok_barang_keluar_detail')->get();
        
        $insertedKD = 0;
        $skippedKD = 0;
        foreach ($keluarDetails as $d) {
            try {
                DB::table('stok_barang_keluar_detail')->insert([
                    'id' => $d->id,
                    'id_barang_keluar' => $d->id_barang_keluar,
                    'id_barang' => $d->id_barang,
                    'qty_barang' => $d->qty_barang ?? 0,
                    'created_at' => $d->created_at,
                    'updated_at' => $d->updated_at,
                ]);
                $insertedKD++;
            } catch (\Exception $e) {
                $skippedKD++;
            }
        }
        $this->info("  -> {$insertedKD} barang keluar detail migrated, {$skippedKD} skipped");

        // 3. Migrate stok_barang_harga
        $this->info('Migrating stok_barang_harga...');
        $hargas = DB::connection('laporit')->table('stok_barang_harga')->get();
        
        $insertedHarga = 0;
        $skippedHarga = 0;
        $failedHarga = []; // Track failed records
        foreach ($hargas as $h) {
            try {
                DB::table('stok_barang_harga')->insert([
                    'id' => $h->id,
                    'id_barang_masuk' => $h->id_barang_masuk,
                    'id_barang_keluar' => $h->id_barang_keluar,
                    'id_barang' => $h->id_barang,
                    'qty_barang' => $h->qty_barang,
                    'min_barang' => $h->min_barang,
                    'id_ref_min_barang' => $h->id_ref_min_barang,
                    'harga_barang' => $h->harga_barang,
                    'tanggal_barang' => $h->tanggal_barang,
                    'harga_barang_invoice' => $h->harga_barang_invoice,
                    'created_at' => $h->created_at,
                    'updated_at' => $h->updated_at,
                ]);
                $insertedHarga++;
            } catch (\Exception $e) {
                dd($e);
                $skippedHarga++;
                $failedHarga[] = [
                    'id_barang_masuk' => $h->id_barang_masuk,
                    'id_barang' => $h->id_barang,
                    'error' => $e->getMessage(),
                ];
            }
        }
        $this->info("  -> {$insertedHarga} harga migrated, {$skippedHarga} skipped");

        // Verification
        $this->newLine();
        $this->info('VERIFICATION:');
        $this->table(
            ['Table', 'Source (laporit)', 'Target (jne_stok)'],
            [
                ['stok_order', $oldOrder, DB::table('stok_order')->count()],
                ['stok_order_detail', $oldOrderDetail, DB::table('stok_order_detail')->count()],
                ['stok_barang_keluar', $oldKeluar, DB::table('stok_barang_keluar')->count()],
                ['stok_barang_keluar_detail', $oldKeluarDetail, DB::table('stok_barang_keluar_detail')->count()],
                ['stok_barang_harga', $oldHarga, DB::table('stok_barang_harga')->count()],
            ]
        );

        $this->info('Step 4 completed!');
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

    private function showSampleOrderData(): void
    {
        $this->newLine();
        $this->info('SAMPLE ORDER DATA (first 5 rows):');
        
        $samples = DB::connection('laporit')
            ->table('stok_order')
            ->select('id', 'no_order', 'tanggal', 'created_by', 'approved_by', 'rejected_by')
            ->limit(5)
            ->get();

        $rows = [];
        foreach ($samples as $s) {
            $mappedCreatedBy = $this->mapUserId($s->created_by);
            $mappedApprovedBy = $this->mapUserId($s->approved_by);
            $rows[] = [
                $s->id,
                $s->no_order,
                $s->tanggal,
                $s->created_by . ($mappedCreatedBy != $s->created_by ? " -> {$mappedCreatedBy}" : ''),
                $s->approved_by . ($mappedApprovedBy != $s->approved_by ? " -> {$mappedApprovedBy}" : ''),
            ];
        }
        $this->table(['ID', 'No Order', 'Tanggal', 'Created By', 'Approved By'], $rows);
    }
}
