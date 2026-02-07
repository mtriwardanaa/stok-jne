<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUserMapping extends Command
{
    protected $signature = 'migrate:user-mapping 
        {--create-missing : Create users in SSO if not exists}
        {--with-master : Also migrate departments, partners, regions, groups}';
    protected $description = 'Map old user IDs to SSO and optionally create missing users/master data';

    protected array $partnerDivisiIds = [7, 8, 9, 13, 23];

    public function handle()
    {
        $createMissing = $this->option('create-missing');
        $withMaster = $this->option('with-master');
        
        $this->info('Starting migration...');

        if ($withMaster) {
            $this->migrateMasterData();
        }

        // Refresh lookups after master data migration
        $ssoGroups = DB::connection('sso_mysql')->table('groups')->get()->keyBy('name');
        $ssoDepartments = DB::connection('sso_mysql')->table('departments')->get()->keyBy('name');

        $oldUsers = DB::connection('laporit')
            ->table('users')
            ->select('id', 'username', 'nama', 'nama_lengkap', 'id_divisi', 'id_agen_kategori', 'no_hp', 'password', 'level', 'active')
            ->get();

        $this->info("Found {$oldUsers->count()} users in old database");

        $tables = [
            'stok_barang_masuk' => ['created_by', 'updated_by'],
            'stok_order' => ['created_by', 'updated_by', 'approved_by', 'rejected_by'],
            'stok_barang_keluar' => ['created_by', 'updated_by'],
        ];

        $stats = ['mapped' => 0, 'created' => 0, 'skipped' => 0, 'groups' => 0];

        foreach ($oldUsers as $old) {
            $ssoUser = DB::connection('sso_mysql')
                ->table('users')
                ->where('username', $old->username)
                ->first();

            $isPartner = in_array($old->id_divisi, $this->partnerDivisiIds);
            
            if (!$ssoUser) {
                if ($createMissing) {
                    $newId = $this->createSsoUser($old, $isPartner, $ssoGroups, $ssoDepartments);
                    if ($newId) {
                        $this->info("Created: {$old->username} (ID: {$newId})");
                        $stats['created']++;
                        $this->updateStokTables($tables, $old->id, $newId);
                    }
                } else {
                    $stats['skipped']++;
                }
                continue;
            }

            // Update group_id for partner users
            if ($isPartner && !$ssoUser->group_id) {
                $group = $this->findGroup($old->nama, $ssoGroups);
                if ($group) {
                    DB::connection('sso_mysql')->table('users')
                        ->where('id', $ssoUser->id)
                        ->update(['group_id' => $group->id, 'type' => 'partner']);
                    $stats['groups']++;
                }
            }

            if ($old->id != $ssoUser->id) {
                $this->updateStokTables($tables, $old->id, $ssoUser->id);
            }
            $stats['mapped']++;
        }

        $this->newLine();
        $this->info("Migration complete!");
        $this->table(['Metric', 'Count'], [
            ['Users mapped', $stats['mapped']],
            ['Users created', $stats['created']],
            ['Groups assigned', $stats['groups']],
            ['Skipped', $stats['skipped']],
        ]);

        return Command::SUCCESS;
    }

    private function migrateMasterData(): void
    {
        $this->info('Migrating master data...');

        // 1. Departments (internal divisi)
        $divisi = DB::connection('laporit')->table('divisi')
            ->whereNotIn('id', $this->partnerDivisiIds)->get();
        foreach ($divisi as $d) {
            DB::connection('sso_mysql')->table('departments')->updateOrInsert(
                ['name' => $d->nama],
                ['name' => $d->nama, 'created_at' => now(), 'updated_at' => now()]
            );
        }
        $this->info("  Departments: {$divisi->count()}");

        // 2. Partners (partner divisi)
        $partnerDivisi = DB::connection('laporit')->table('divisi')
            ->whereIn('id', $this->partnerDivisiIds)->get();
        $partnerMap = []; // old id => new id
        foreach ($partnerDivisi as $p) {
            DB::connection('sso_mysql')->table('partners')->updateOrInsert(
                ['name' => $p->nama],
                ['name' => $p->nama, 'created_at' => now(), 'updated_at' => now()]
            );
            $newPartner = DB::connection('sso_mysql')->table('partners')->where('name', $p->nama)->first();
            $partnerMap[$p->id] = $newPartner->id;
        }
        $this->info("  Partners: {$partnerDivisi->count()}");

        // 3. Regions (agen_kategori)
        $regions = DB::connection('laporit')->table('agen_kategori')->get();
        $regionMap = []; // old id => new id
        foreach ($regions as $r) {
            DB::connection('sso_mysql')->table('regions')->updateOrInsert(
                ['name' => $r->nama],
                ['name' => $r->nama, 'created_at' => now(), 'updated_at' => now()]
            );
            $newRegion = DB::connection('sso_mysql')->table('regions')->where('name', $r->nama)->first();
            $regionMap[$r->id] = $newRegion->id;
        }
        $this->info("  Regions: {$regions->count()}");

        // 4. Groups (from unique nama of partner users) with partner_id and region_id
        $partnerUsers = DB::connection('laporit')->table('users')
            ->whereIn('id_divisi', $this->partnerDivisiIds)
            ->select('nama', 'id_divisi', 'id_agen_kategori')
            ->distinct()
            ->get()
            ->unique('nama');
        
        $groupCount = 0;
        foreach ($partnerUsers as $u) {
            if (!$u->nama) continue;
            
            $partnerId = $partnerMap[$u->id_divisi] ?? null;
            $regionId = $u->id_agen_kategori ? ($regionMap[$u->id_agen_kategori] ?? null) : null;
            
            DB::connection('sso_mysql')->table('groups')->updateOrInsert(
                ['name' => $u->nama],
                [
                    'name' => $u->nama,
                    'partner_id' => $partnerId,
                    'region_id' => $regionId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $groupCount++;
        }
        $this->info("  Groups: {$groupCount}");
    }

    private function createSsoUser($old, bool $isPartner, $ssoGroups, $ssoDepartments): ?int
    {
        $groupId = null;
        if ($isPartner) {
            $group = $this->findGroup($old->nama, $ssoGroups);
            $groupId = $group?->id;
        }

        try {
            return DB::connection('sso_mysql')->table('users')->insertGetId([
                'name' => $old->nama_lengkap ?? $old->nama,
                'username' => $old->username,
                'password' => $old->password,
                'phone' => $old->no_hp ?? '',
                'role' => $old->level === 'admin' ? 'admin' : 'staff',
                'type' => $isPartner ? 'partner' : 'internal',
                'group_id' => $groupId,
                'department_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->error("Failed: {$old->username} - {$e->getMessage()}");
            return null;
        }
    }

    private function findGroup(string $name, $ssoGroups)
    {
        return $ssoGroups->get($name) ?? $ssoGroups->first(fn($g) => 
            stripos($g->name, $name) !== false || stripos($name, $g->name) !== false
        );
    }

    private function updateStokTables(array $tables, int $oldId, int $newId): void
    {
        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)->where($column, $oldId)->update([$column => $newId]);
            }
        }
    }
}
