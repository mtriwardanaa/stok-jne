<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStep1Users extends Command
{
    protected $signature = 'migrate:step1-users 
        {--dry-run : Show what would be done without executing}
        {--skip-restore : Skip restore SSO data from backup}
        {--show-backup : Show available backup files}';

    protected $description = 'Step 1: Restore SSO, migrate master data, and create missing users';

    protected array $partnerDivisiIds = [7, 8, 9, 13, 23, 29];
    protected array $userMapping = []; // old_id => new_id
    protected array $divisiToDeptMap = []; // old divisi_id => sso department_id
    protected array $namaToGroupMap = []; // nama => sso group_id
    protected array $partnerMap = []; // old divisi_id => sso partner_id
    protected array $regionMap = []; // old agen_kategori_id => sso region_id
    protected string $backupPath;

    public function __construct()
    {
        parent::__construct();
        $this->backupPath = storage_path('app/migration_backup');
    }

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $skipRestore = true;
        $showBackup = $this->option('show-backup');
        
        $this->info('===========================================');
        $this->info('STEP 1: USER MIGRATION');
        $this->info('===========================================');

        // Show available backups
        if ($showBackup) {
            $this->showAvailableBackups();
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // STEP 1: Restore SSO data from backup
        if (!$skipRestore && !$dryRun) {
            $this->info('');
            $this->info('[1/4] RESTORE SSO DATA FROM BACKUP');
            $this->info('-----------------------------------');
            if (!$this->restoreSsoData()) {
                return Command::FAILURE;
            }
        } else {
            $this->info('[1/4] RESTORE SSO DATA - SKIPPED');
        }

        // STEP 2: Migrate master data (departments, partners, regions, groups)
        $this->info('');
        $this->info('[2/4] MIGRATE MASTER DATA');
        $this->info('-------------------------');
        $this->migrateMasterData($dryRun);

        // STEP 3: Build mappings
        $this->info('');
        $this->info('[3/4] BUILD MAPPINGS');
        $this->info('--------------------');
        $this->buildDepartmentGroupMappings();

        // STEP 4: Check and create missing users
        $this->info('');
        $this->info('[4/4] CHECK & CREATE MISSING USERS');
        $this->info('-----------------------------------');
        $this->checkAndCreateMissingUsers($dryRun);

        // Save mapping to file
        $this->saveMappingToFile();

        $this->info('');
        $this->info('===========================================');
        $this->info('STEP 1 COMPLETED!');
        $this->info('===========================================');

        return Command::SUCCESS;
    }

    private function restoreSsoData(): bool
    {
        if (!is_dir($this->backupPath)) {
            $this->error('No backups found');
            return false;
        }

        // Get latest backup directory
        $backups = array_filter(
            array_diff(scandir($this->backupPath, SCANDIR_SORT_DESCENDING), ['.', '..']),
            fn($b) => is_dir("{$this->backupPath}/{$b}")
        );
        
        if (empty($backups)) {
            $this->error('No backups found');
            return false;
        }

        $latestBackup = reset($backups);
        $backupDir = "{$this->backupPath}/{$latestBackup}";
        
        $this->info("Restoring from: {$latestBackup}");

        if (!$this->confirm('This will REPLACE all SSO data. Continue?', true)) {
            return false;
        }

        DB::connection('sso_mysql')->statement('SET FOREIGN_KEY_CHECKS = 0');

        $tablesToRestore = ['regions', 'partners', 'departments', 'groups', 'users'];

        foreach ($tablesToRestore as $table) {
            $file = "{$backupDir}/{$table}.json";
            
            if (!file_exists($file)) {
                $this->warn("  -> {$table}: not found, skipping");
                continue;
            }

            $data = json_decode(file_get_contents($file), true);
            DB::connection('sso_mysql')->table($table)->truncate();
            
            if (!empty($data)) {
                foreach (array_chunk($data, 100) as $chunk) {
                    DB::connection('sso_mysql')->table($table)->insert(
                        array_map(fn($row) => (array) $row, $chunk)
                    );
                }
            }
            
            $this->info("  -> {$table}: " . count($data) . " records restored");
        }

        DB::connection('sso_mysql')->statement('SET FOREIGN_KEY_CHECKS = 1');
        $this->info('SSO data restored!');
        return true;
    }

    private function migrateMasterData(bool $dryRun): void
    {
        // Partner divisi ID to SSO partner name mapping
        // 7 (KP SIANTAN), 8 (KP NGABANG), 9 (KP ENTIKONG) -> KANTOR PERWAKILAN
        // 13 (AGEN HYBRID) -> AGEN HYBRID
        // 23 (SUB AGEN / CABANG) -> CABANG / SUB AGEN
        $divisiToPartnerName = [
            7 => 'KANTOR PERWAKILAN',   // KP SIANTAN
            8 => 'KANTOR PERWAKILAN',   // KP NGABANG
            9 => 'KANTOR PERWAKILAN',   // KP ENTIKONG
            13 => 'AGEN HYBRID',        // AGEN HYBRID
            23 => 'CABANG / SUB AGEN',  // SUB AGEN / CABANG
            29 => 'CUSTOMER CORPORATE', // CUSTOMER CORPORATE
        ];

        // 1. Departments (internal divisi - NOT partner)
        $divisi = DB::connection('laporit')->table('divisi')
            ->whereNotIn('id', $this->partnerDivisiIds)->get();
        
        $this->info("Departments from internal divisi: {$divisi->count()}");
        if (!$dryRun) {
            foreach ($divisi as $d) {
                DB::connection('sso_mysql')->table('departments')->updateOrInsert(
                    ['name' => $d->nama],
                    ['name' => $d->nama, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 2. Build partner map from existing SSO partners (NO NEW PARTNERS CREATED)
        $ssoPartners = DB::connection('sso_mysql')->table('partners')->get()->keyBy('name');
        
        foreach ($divisiToPartnerName as $divisiId => $partnerName) {
            $partner = $ssoPartners->get($partnerName);
            if ($partner) {
                $this->partnerMap[$divisiId] = $partner->id;
                $this->info("  Divisi {$divisiId} -> Partner '{$partnerName}' (ID: {$partner->id})");
            } else {
                $this->warn("  Partner '{$partnerName}' not found in SSO!");
            }
        }

        // 3. Regions (agen_kategori)
        $regions = DB::connection('laporit')->table('agen_kategori')->get();
        
        $this->info("Regions from agen_kategori: {$regions->count()}");
        if (!$dryRun) {
            foreach ($regions as $r) {
                DB::connection('sso_mysql')->table('regions')->updateOrInsert(
                    ['name' => $r->nama],
                    ['name' => $r->nama, 'created_at' => now(), 'updated_at' => now()]
                );
                $newRegion = DB::connection('sso_mysql')->table('regions')->where('name', $r->nama)->first();
                $this->regionMap[$r->id] = $newRegion->id;
            }
        }

        // 4. Groups (from unique nama of partner users)
        $partnerUsers = DB::connection('laporit')->table('users')
            ->whereIn('id_divisi', $this->partnerDivisiIds)
            ->select('nama', 'id_divisi', 'id_agen_kategori')
            ->distinct()
            ->get()
            ->unique('nama');
        
        $this->info("Groups from partner users: {$partnerUsers->count()}");
        if (!$dryRun) {
            foreach ($partnerUsers as $u) {
                if (!$u->nama) continue;
                
                $partnerId = $this->partnerMap[$u->id_divisi] ?? null;
                $regionId = $u->id_agen_kategori ? ($this->regionMap[$u->id_agen_kategori] ?? null) : null;
                
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
            }
        }
    }

    private function buildDepartmentGroupMappings(): void
    {
        // Get old divisi
        $oldDivisi = DB::connection('laporit')->table('divisi')->get()->keyBy('id');

        // Get SSO departments
        $ssoDepts = DB::connection('sso_mysql')->table('departments')->get()->keyBy('name');

        foreach ($oldDivisi as $divisi) {
            if (!in_array($divisi->id, $this->partnerDivisiIds)) {
                $dept = $ssoDepts->get($divisi->nama);
                if ($dept) {
                    $this->divisiToDeptMap[$divisi->id] = $dept->id;
                }
            }
        }

        // Get SSO groups
        $ssoGroups = DB::connection('sso_mysql')->table('groups')->get()->keyBy('name');

        foreach ($ssoGroups as $group) {
            $this->namaToGroupMap[$group->name] = $group->id;
        }

        $this->info("Mapped {$ssoDepts->count()} departments, {$ssoGroups->count()} groups");
    }

    private function checkAndCreateMissingUsers(bool $dryRun): void
    {
        $oldUsers = DB::connection('laporit')
            ->table('users')
            ->select('id', 'username', 'nama', 'nama_lengkap', 'id_divisi', 'id_agen_kategori', 'no_hp', 'password', 'level', 'active')
            ->get();

        $ssoUsers = DB::connection('sso_mysql')
            ->table('users')
            ->get()
            ->keyBy('username');

        $this->info("Laporit users: {$oldUsers->count()}, SSO users: {$ssoUsers->count()}");

        $missingUsers = [];
        $stats = ['found' => 0, 'same_id' => 0, 'created' => 0];

        foreach ($oldUsers as $old) {
            if ($old->username == 'tobib') {
                dd($old);
            }
            $ssoUser = $ssoUsers->get($old->username);
            $isPartner = in_array($old->id_divisi, $this->partnerDivisiIds);
            
            if ($ssoUser) {
                $this->userMapping[$old->id] = $ssoUser->id;
                $old->id == $ssoUser->id ? $stats['same_id']++ : $stats['found']++;
            } else {
                $missingUsers[] = [
                    'old' => $old,
                    'isPartner' => $isPartner,
                    'departmentId' => $isPartner ? null : ($this->divisiToDeptMap[$old->id_divisi] ?? null),
                    'groupId' => $isPartner ? ($this->namaToGroupMap[$old->nama] ?? null) : null,
                ];
            }
        }

        $this->info("Found in SSO: " . ($stats['found'] + $stats['same_id']) . ", Missing: " . count($missingUsers));

        if (count($missingUsers) > 0) {
            $this->warn("Creating " . count($missingUsers) . " missing users...");
            
            $rows = [];
            foreach ($missingUsers as $m) {
                $old = $m['old'];
                $rows[] = [$old->id, $old->username, substr($old->nama ?? '-', 0, 20), $m['isPartner'] ? 'partner' : 'internal'];
                
                if (!$dryRun) {
                    $newId = $this->createSsoUser($old, $m['isPartner'], $m['departmentId'], $m['groupId']);
                    if ($newId) {
                        $this->userMapping[$old->id] = $newId;
                        $stats['created']++;
                    }
                }
            }
            
            $this->table(['Old ID', 'Username', 'Nama', 'Type'], $rows);
            $this->info("Created: {$stats['created']} users");
        } else {
            $this->info('All users already exist in SSO!');
        }
    }

    private function createSsoUser($old, bool $isPartner, ?int $departmentId, ?int $groupId): ?int
    {
        try {
            return DB::connection('sso_mysql')->table('users')->insertGetId([
                'name' => $old->nama_lengkap ?? $old->nama ?? $old->username,
                'username' => $old->username,
                'password' => $old->password,
                'phone' => $old->no_hp ?? '',
                'role' => $old->level === 'admin' ? 'admin' : 'staff',
                'type' => $isPartner ? 'partner' : 'internal',
                'group_id' => $groupId,
                'department_id' => $departmentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->error("Failed: {$old->username} - {$e->getMessage()}");
            return null;
        }
    }

    private function showAvailableBackups(): void
    {
        if (!is_dir($this->backupPath)) {
            $this->warn('No backups found');
            return;
        }

        $backups = array_filter(
            array_diff(scandir($this->backupPath, SCANDIR_SORT_DESCENDING), ['.', '..']),
            fn($b) => is_dir("{$this->backupPath}/{$b}")
        );
        
        if (empty($backups)) {
            $this->warn('No backups found');
            return;
        }

        $rows = [];
        foreach ($backups as $backup) {
            $infoFile = "{$this->backupPath}/{$backup}/backup_info.json";
            if (file_exists($infoFile)) {
                $info = json_decode(file_get_contents($infoFile), true);
                $rows[] = [$backup, $info['created_at'] ?? '-'];
            }
        }

        $this->table(['Backup Dir', 'Created At'], $rows);
    }

    private function saveMappingToFile(): void
    {
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }

        $mappingFile = "{$this->backupPath}/user_mapping.json";
        file_put_contents($mappingFile, json_encode($this->userMapping, JSON_PRETTY_PRINT));
        $this->info("User mapping saved: {$mappingFile}");
    }
}
