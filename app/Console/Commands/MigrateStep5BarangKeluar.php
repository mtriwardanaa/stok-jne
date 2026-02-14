<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep5BarangKeluar extends Command
{
    protected $signature = 'migrate:step5-barang-keluar 
        {--dry-run : Show what would be done without executing}';
    protected $description = 'Step 5: Migrate stok_barang_keluar, detail & harga (with dept/group mapping)';

    protected array $userMapping = [];       // old_user_id => sso_user_id
    protected array $ssoUserDeptMap = [];     // sso_user_id => department_id
    protected array $ssoUserGroupMap = [];    // sso_user_id => group_id
    protected array $divisiToDeptMap = [];    // old_divisi_id => sso_department_id
    protected array $divisiToGroupMap = [];   // old_divisi_id => sso_group_id
    protected array $partnerDivisiIds = [7, 8, 9, 13, 23, 29];
    protected $oldOrders;                    // id => order object (for created_by lookup)

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('===========================================');
        $this->info('STEP 5: BARANG KELUAR MIGRATION');
        $this->info('===========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Build all mappings
        $this->buildUserMapping();
        $this->buildSsoUserOrgMap();
        $this->buildDivisiMapping();
        $this->loadOldOrders();

        // Count source data
        $oldKeluar = DB::connection('laporit')->table('stok_barang_keluar')->count();
        $oldKeluarDetail = DB::connection('laporit')->table('stok_barang_keluar_detail')->count();
        $oldHarga = DB::connection('laporit')->table('stok_barang_harga')->count();

        $this->info("Source data from laporit:");
        $this->table(['Table', 'Count'], [
            ['stok_barang_keluar', $oldKeluar],
            ['stok_barang_keluar_detail', $oldKeluarDetail],
            ['stok_barang_harga', $oldHarga],
        ]);

        if ($dryRun) {
            $this->showSampleBarangKeluarData();
            return Command::SUCCESS;
        }

        // Truncate
        $this->warn('Truncating target tables...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('stok_barang_harga')->truncate();
        DB::table('stok_barang_keluar_detail')->truncate();
        DB::table('stok_barang_keluar')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        $this->info('Tables truncated');

        // 1. Migrate stok_barang_keluar
        $this->info('Migrating stok_barang_keluar...');
        $keluars = DB::connection('laporit')->table('stok_barang_keluar')->get();
        
        $insertedKeluar = 0;
        $skippedKeluar = 0;
        $stats = ['from_order' => 0, 'from_agen' => 0, 'from_divisi' => 0, 'no_org' => 0];

        foreach ($keluars as $k) {
            $createdBy = $this->mapUserId($k->created_by);
            $updatedBy = $this->mapUserId($k->updated_by);

            // Resolve user_id, department_id, group_id
            $resolved = $this->resolveOrganization($k);
            
            // Track stats
            $stats[$resolved['source']]++;
            
            try {
                DB::table('stok_barang_keluar')->insert([
                    'id' => $k->id,
                    'no_barang_keluar' => $k->no_barang_keluar,
                    'tanggal' => $k->tanggal,
                    'department_id' => $resolved['department_id'],
                    'group_id' => $resolved['group_id'],
                    'user_id' => $resolved['user_id'],
                    'order_id' => $k->id_order ?? null,
                    'nama_user_request' => $k->nama_user_request,
                    'distribusi_sales' => $k->distribusi_sales == 1 ? 'yes' : null,
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'created_at' => $k->created_at,
                    'updated_at' => $k->updated_at,
                    'is_old' => true,
                ]);
                $insertedKeluar++;
            } catch (\Exception $e) {
                $skippedKeluar++;
                $this->error("  Keluar #{$k->id}: " . $e->getMessage());
            }
        }
        $this->info("  -> {$insertedKeluar} barang keluar migrated, {$skippedKeluar} skipped");
        $this->info("  -> Source: from_order={$stats['from_order']}, from_agen={$stats['from_agen']}, from_divisi={$stats['from_divisi']}, no_org={$stats['no_org']}");

        // 2. Migrate stok_barang_keluar_detail
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
                $this->error("  Detail #{$d->id}: " . $e->getMessage());
            }
        }
        $this->info("  -> {$insertedKD} barang keluar detail migrated, {$skippedKD} skipped");

        // 3. Migrate stok_barang_harga
        $this->info('Migrating stok_barang_harga...');
        $hargas = DB::connection('laporit')->table('stok_barang_harga')->get();
        
        $insertedHarga = 0;
        $skippedHarga = 0;
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
                $skippedHarga++;
                $this->error("  Harga #{$h->id}: " . $e->getMessage());
            }
        }
        $this->info("  -> {$insertedHarga} harga migrated, {$skippedHarga} skipped");

        // Verification
        $this->newLine();
        $this->info('VERIFICATION:');
        $this->table(
            ['Table', 'Source (laporit)', 'Target (jne_stok)'],
            [
                ['stok_barang_keluar', $oldKeluar, DB::table('stok_barang_keluar')->count()],
                ['stok_barang_keluar_detail', $oldKeluarDetail, DB::table('stok_barang_keluar_detail')->count()],
                ['stok_barang_harga', $oldHarga, DB::table('stok_barang_harga')->count()],
            ]
        );

        $this->info('Step 5 (Barang Keluar) completed!');
        return Command::SUCCESS;
    }

    /**
     * Resolve department_id, group_id, user_id from old record
     * Priority:
     * 1. id_order → get order's created_by → map to SSO user → get dept/group
     * 2. id_agen → map to SSO user → get dept/group
     * 3. id_divisi → use divisi mapping (internal→dept, partner→null)
     */
    private function resolveOrganization($k): array
    {
        $result = [
            'department_id' => null,
            'group_id' => null,
            'user_id' => null,
            'source' => 'no_org',
        ];

        // Priority 1: From order
        if (!empty($k->id_order) && isset($this->oldOrders[$k->id_order])) {
            $order = $this->oldOrders[$k->id_order];
            $ssoUserId = $this->mapUserId($order->created_by);
            
            if ($ssoUserId) {
                $result['user_id'] = $ssoUserId;
                $result['department_id'] = $this->ssoUserDeptMap[$ssoUserId] ?? null;
                $result['group_id'] = $this->ssoUserGroupMap[$ssoUserId] ?? null;
                $result['source'] = 'from_order';
            }
            return $result;
        }

        // Priority 2: From id_agen (maps to user)
        if (!empty($k->id_agen)) {
            $ssoUserId = $this->mapUserId($k->id_agen);
            
            if ($ssoUserId) {
                $result['user_id'] = $ssoUserId;
                $result['department_id'] = $this->ssoUserDeptMap[$ssoUserId] ?? null;
                $result['group_id'] = $this->ssoUserGroupMap[$ssoUserId] ?? null;
                $result['source'] = 'from_agen';
            }
            return $result;
        }

        // Priority 3: From id_divisi (use divisi mapping)
        if (!empty($k->id_divisi)) {
            if (in_array($k->id_divisi, $this->partnerDivisiIds)) {
                // Partner divisi → map to group
                $result['group_id'] = $this->divisiToGroupMap[$k->id_divisi] ?? null;
                $result['source'] = 'from_divisi';
            } else {
                $result['department_id'] = $this->divisiToDeptMap[$k->id_divisi] ?? null;
                $result['source'] = 'from_divisi';
            }
        }

        return $result;
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

    private function buildSsoUserOrgMap(): void
    {
        $this->info('Building SSO user → dept/group map...');
        
        $ssoUsers = DB::connection('sso_mysql')
            ->table('users')
            ->select('id', 'department_id', 'group_id')
            ->get();

        foreach ($ssoUsers as $u) {
            if ($u->department_id) {
                $this->ssoUserDeptMap[$u->id] = $u->department_id;
            }
            if ($u->group_id) {
                $this->ssoUserGroupMap[$u->id] = $u->group_id;
            }
        }
        
        $this->info("  -> " . count($this->ssoUserDeptMap) . " users with dept, " . count($this->ssoUserGroupMap) . " with group");
    }

    private function buildDivisiMapping(): void
    {
        $this->info('Building divisi → department/group mapping...');
        
        $oldDivisi = DB::connection('laporit')->table('divisi')->get()->keyBy('id');
        $ssoDepts = DB::connection('sso_mysql')->table('departments')->get()->keyBy('name');
        $ssoGroups = DB::connection('sso_mysql')->table('groups')->get();

        // Build name-to-group lookup (lowercased for fuzzy matching)
        $groupByName = [];
        foreach ($ssoGroups as $g) {
            $groupByName[strtolower(trim($g->name))] = $g->id;
        }

        foreach ($oldDivisi as $divisi) {
            if (in_array($divisi->id, $this->partnerDivisiIds)) {
                // Partner divisi → match to SSO group by name
                $name = strtolower(trim($divisi->nama));
                if (isset($groupByName[$name])) {
                    $this->divisiToGroupMap[$divisi->id] = $groupByName[$name];
                    $this->info("  -> Divisi '" . $divisi->nama . "' (ID=" . $divisi->id . ") => Group ID=" . $groupByName[$name]);
                } else {
                    $this->warn("  -> Divisi '" . $divisi->nama . "' (ID=" . $divisi->id . ") => No matching group found");
                }
            } else {
                $dept = $ssoDepts->get($divisi->nama);
                if ($dept) {
                    $this->divisiToDeptMap[$divisi->id] = $dept->id;
                }
            }
        }
        
        $this->info("  -> " . count($this->divisiToDeptMap) . " divisi mapped to departments");
        $this->info("  -> " . count($this->divisiToGroupMap) . " divisi mapped to groups");
    }

    private function loadOldOrders(): void
    {
        $this->info('Loading old orders for lookup...');
        $orders = DB::connection('laporit')
            ->table('stok_order')
            ->select('id', 'created_by')
            ->get();
        
        $this->oldOrders = $orders->keyBy('id')->toArray();
        $this->info("  -> " . count($this->oldOrders) . " orders loaded");
    }

    private function mapUserId($oldId): ?int
    {
        if ($oldId === null) return null;
        return $this->userMapping[$oldId] ?? $oldId;
    }

    private function showSampleBarangKeluarData(): void
    {
        $this->newLine();
        $this->info('SAMPLE BARANG KELUAR (first 15):');
        
        $samples = DB::connection('laporit')
            ->table('stok_barang_keluar')
            ->select('id', 'no_barang_keluar', 'id_divisi', 'id_order', 'id_agen', 'created_by', 'nama_user_request')
            ->limit(15)
            ->get();

        $rows = [];
        foreach ($samples as $s) {
            $resolved = $this->resolveOrganization($s);
            $mappedUser = $this->mapUserId($s->created_by);
            $rows[] = [
                $s->id,
                substr($s->no_barang_keluar, 0, 15),
                $s->id_order ?? '-',
                $s->id_agen ?? '-',
                $s->id_divisi ?? '-',
                $resolved['source'],
                $resolved['department_id'] ?? '-',
                $resolved['group_id'] ?? '-',
                $resolved['user_id'] ?? '-',
                $s->created_by . ($mappedUser != $s->created_by ? "→{$mappedUser}" : ''),
            ];
        }
        $this->table([
            'ID', 'No BK', 'Order', 'Agen', 'Divisi',
            'Source', 'Dept', 'Group', 'User', 'Created By',
        ], $rows);
    }
}
