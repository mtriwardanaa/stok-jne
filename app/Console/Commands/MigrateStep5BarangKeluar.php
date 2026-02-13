<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep5BarangKeluar extends Command
{
    protected $signature = 'migrate:step5-barang-keluar 
        {--dry-run : Show what would be done without executing}
        {--truncate : Truncate target tables before migration}';
    protected $description = 'Step 5: Migrate stok_barang_keluar, detail & harga (with divisi mapping)';

    protected array $userMapping = [];
    protected array $divisiToDeptMap = [];
    protected array $kategoriToRegionMap = [];
    protected array $partnerDivisiIds = [7, 8, 9, 13, 23, 29];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $truncate = true;
        
        $this->info('===========================================');
        $this->info('STEP 5: BARANG KELUAR MIGRATION');
        $this->info('===========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Build all mappings
        $this->buildUserMapping();
        $this->buildDivisiMapping();
        $this->buildKategoriMapping();

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
        if ($truncate) {
            $this->warn('Truncating target tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('stok_barang_harga')->truncate();
            DB::table('stok_barang_keluar_detail')->truncate();
            DB::table('stok_barang_keluar')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info('Tables truncated');
        }

        // 1. Migrate stok_barang_keluar
        $this->info('Migrating stok_barang_keluar...');
        $keluars = DB::connection('laporit')->table('stok_barang_keluar')->get();
        
        $insertedKeluar = 0;
        $skippedKeluar = 0;
        $divisiMapped = 0;
        $divisiNulled = 0;
        foreach ($keluars as $k) {
            $createdBy = $this->mapUserId($k->created_by);
            $updatedBy = $this->mapUserId($k->updated_by);

            // Map divisi → department
            $newDivisiId = $this->mapDivisiId($k->id_divisi);
            if ($k->id_divisi !== null && $newDivisiId !== null) $divisiMapped++;
            if ($k->id_divisi !== null && $newDivisiId === null) $divisiNulled++;

            // Map kategori → region
            $newKategoriId = $this->mapKategoriId($k->id_kategori);
            
            try {
                DB::table('stok_barang_keluar')->insert([
                    'id' => $k->id,
                    'no_barang_keluar' => $k->no_barang_keluar,
                    'tanggal' => $k->tanggal,
                    'id_divisi' => $newDivisiId,
                    'id_kategori' => $newKategoriId,
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
                $this->error("  Keluar #{$k->id} skipped: " . $e->getMessage());
            }
        }
        $this->info("  -> {$insertedKeluar} barang keluar migrated, {$skippedKeluar} skipped");
        $this->info("  -> Divisi mapped: {$divisiMapped}, set NULL (partner): {$divisiNulled}");

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
                $this->error("  Detail #{$d->id} skipped: " . $e->getMessage());
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
                $this->error("  Harga #{$h->id} skipped: " . $e->getMessage());
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

    private function buildDivisiMapping(): void
    {
        $this->info('Building divisi → department mapping...');
        
        $oldDivisi = DB::connection('laporit')->table('divisi')->get()->keyBy('id');
        $ssoDepts = DB::connection('sso_mysql')->table('departments')->get()->keyBy('name');

        foreach ($oldDivisi as $divisi) {
            if (!in_array($divisi->id, $this->partnerDivisiIds)) {
                $dept = $ssoDepts->get($divisi->nama);
                if ($dept) {
                    $this->divisiToDeptMap[$divisi->id] = $dept->id;
                    $this->info("  -> Divisi #{$divisi->id} ({$divisi->nama}) → Dept #{$dept->id}");
                } else {
                    $this->warn("  -> Divisi #{$divisi->id} ({$divisi->nama}) → NOT FOUND in SSO!");
                }
            } else {
                $this->info("  -> Divisi #{$divisi->id} ({$divisi->nama}) → NULL (partner)");
            }
        }
    }

    private function buildKategoriMapping(): void
    {
        $this->info('Building kategori → region mapping...');
        
        $oldKategori = DB::connection('laporit')->table('agen_kategori')->get();
        $ssoRegions = DB::connection('sso_mysql')->table('regions')->get()->keyBy('name');

        foreach ($oldKategori as $k) {
            $region = $ssoRegions->get($k->nama);
            if ($region) {
                $this->kategoriToRegionMap[$k->id] = $region->id;
                $this->info("  -> Kategori #{$k->id} ({$k->nama}) → Region #{$region->id}");
            } else {
                $this->warn("  -> Kategori #{$k->id} ({$k->nama}) → NOT FOUND!");
            }
        }
    }

    private function mapUserId($oldId): ?int
    {
        if ($oldId === null) return null;
        return $this->userMapping[$oldId] ?? $oldId;
    }

    private function mapDivisiId($oldId): ?int
    {
        if ($oldId === null) return null;
        if (in_array($oldId, $this->partnerDivisiIds)) return null;
        return $this->divisiToDeptMap[$oldId] ?? null;
    }

    private function mapKategoriId($oldId): ?int
    {
        if ($oldId === null) return null;
        return $this->kategoriToRegionMap[$oldId] ?? null;
    }

    private function showSampleBarangKeluarData(): void
    {
        $this->newLine();
        $this->info('SAMPLE BARANG KELUAR DATA (first 10 rows):');
        
        $samples = DB::connection('laporit')
            ->table('stok_barang_keluar')
            ->select('id', 'no_barang_keluar', 'tanggal', 'id_divisi', 'id_kategori', 'created_by')
            ->limit(10)
            ->get();

        $rows = [];
        foreach ($samples as $s) {
            $newDivisi = $this->mapDivisiId($s->id_divisi);
            $newKategori = $this->mapKategoriId($s->id_kategori);
            $mappedUser = $this->mapUserId($s->created_by);
            $rows[] = [
                $s->id,
                $s->no_barang_keluar,
                $s->id_divisi . ' → ' . ($newDivisi ?? 'NULL'),
                ($s->id_kategori ?? '-') . ' → ' . ($newKategori ?? 'NULL'),
                $s->created_by . ($mappedUser != $s->created_by ? " → {$mappedUser}" : ''),
            ];
        }
        $this->table(['ID', 'No BK', 'Divisi (old→new)', 'Kategori (old→new)', 'Created By'], $rows);
    }
}
