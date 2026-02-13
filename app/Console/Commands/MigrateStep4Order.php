<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep4Order extends Command
{
    protected $signature = 'migrate:step4-order 
        {--dry-run : Show what would be done without executing}';
    protected $description = 'Step 4: Migrate stok_order & stok_order_detail';

    protected array $userMapping = [];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('===========================================');
        $this->info('STEP 4: ORDER MIGRATION');
        $this->info('===========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Build user mapping
        $this->buildUserMapping();

        // Count source data
        $oldOrder = DB::connection('laporit')->table('stok_order')->count();
        $oldOrderDetail = DB::connection('laporit')->table('stok_order_detail')->count();

        $this->info("Source data from laporit:");
        $this->table(['Table', 'Count'], [
            ['stok_order', $oldOrder],
            ['stok_order_detail', $oldOrderDetail],
        ]);

        if ($dryRun) {
            $this->showSampleOrderData();
            return Command::SUCCESS;
        }

        // Truncate
        $this->warn('Truncating target tables...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('stok_order_detail')->truncate();
        DB::table('stok_order')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        $this->info('Tables truncated');

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
                    'id_divisi' => null, // derived from created_by user
                    'id_kategori' => null, // derived from created_by user
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
                $this->error("  Order #{$o->id}: " . $e->getMessage());
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
                $this->error("  Detail #{$d->id}: " . $e->getMessage());
            }
        }
        $this->info("  -> {$insertedOD} order details migrated, {$skippedOD} skipped");

        // Verification
        $this->newLine();
        $this->info('VERIFICATION:');
        $this->table(
            ['Table', 'Source (laporit)', 'Target (jne_stok)'],
            [
                ['stok_order', $oldOrder, DB::table('stok_order')->count()],
                ['stok_order_detail', $oldOrderDetail, DB::table('stok_order_detail')->count()],
            ]
        );

        $this->info('Step 4 (Order) completed!');
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
        if ($oldId === null) return null;
        return $this->userMapping[$oldId] ?? $oldId;
    }

    private function showSampleOrderData(): void
    {
        $this->newLine();
        $this->info('SAMPLE ORDER DATA (first 10):');
        
        $samples = DB::connection('laporit')
            ->table('stok_order')
            ->select('id', 'no_order', 'tanggal', 'id_divisi', 'created_by', 'nama_user_request')
            ->limit(10)
            ->get();

        $rows = [];
        foreach ($samples as $s) {
            $mappedUser = $this->mapUserId($s->created_by);
            $rows[] = [
                $s->id,
                $s->no_order,
                $s->tanggal,
                "{$s->id_divisi} → NULL",
                $s->created_by . ($mappedUser != $s->created_by ? " → {$mappedUser}" : ''),
                substr($s->nama_user_request ?? '-', 0, 20),
            ];
        }
        $this->table(['ID', 'No Order', 'Tanggal', 'Divisi', 'Created By', 'User Request'], $rows);
    }
}
