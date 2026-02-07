<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StokVerify extends Command
{
    protected $signature = 'stok:verify 
        {--show-all : Show all items, not just anomalies}
        {--export : Export to CSV file}';
    protected $description = 'Verify stock quantities by comparing barang masuk and keluar';

    public function handle()
    {
        $showAll = $this->option('show-all');
        $export = $this->option('export');
        
        $this->info('===========================================');
        $this->info('STOCK VERIFICATION');
        $this->info('===========================================');

        // Get all barang
        $barangs = DB::table('stok_barang')
            ->select('id', 'kode_barang', 'nama_barang')
            ->orderBy('nama_barang')
            ->get()
            ->keyBy('id');

        $this->info("Total barang: {$barangs->count()}");

        // Calculate total masuk per barang
        $totalMasuk = DB::table('stok_barang_masuk_detail')
            ->select('id_barang', DB::raw('SUM(qty_barang) as total_masuk'))
            ->groupBy('id_barang')
            ->pluck('total_masuk', 'id_barang')
            ->toArray();

        // Calculate total keluar per barang
        $totalKeluar = DB::table('stok_barang_keluar_detail')
            ->select('id_barang', DB::raw('SUM(qty_barang) as total_keluar'))
            ->groupBy('id_barang')
            ->pluck('total_keluar', 'id_barang')
            ->toArray();

        // Build report
        $rows = [];
        $anomalies = 0;
        $totalStok = 0;

        foreach ($barangs as $id => $barang) {
            $masuk = $totalMasuk[$id] ?? 0;
            $keluar = $totalKeluar[$id] ?? 0;
            $saldo = $masuk - $keluar;
            $totalStok += $saldo;

            $status = 'OK';
            if ($saldo < 0) {
                $status = '⚠️ MINUS';
                $anomalies++;
            } elseif ($masuk == 0 && $keluar == 0) {
                $status = '- KOSONG';
            }

            if ($showAll || $saldo < 0 || ($masuk > 0 || $keluar > 0)) {
                $rows[] = [
                    $barang->kode_barang ?? '-',
                    substr($barang->nama_barang, 0, 30),
                    number_format($masuk),
                    number_format($keluar),
                    number_format($saldo),
                    $status,
                ];
            }
        }

        // Show table
        $this->newLine();
        $this->table(
            ['Kode', 'Nama Barang', 'Masuk', 'Keluar', 'Saldo', 'Status'],
            $rows
        );

        // Summary
        $this->newLine();
        $this->info('===========================================');
        $this->info('SUMMARY');
        $this->info('===========================================');
        $this->table(['Metric', 'Value'], [
            ['Total Barang', $barangs->count()],
            ['Barang dengan transaksi', count($rows)],
            ['Anomali (saldo minus)', $anomalies],
            ['Total Saldo Stok', number_format($totalStok)],
        ]);

        if ($anomalies > 0) {
            $this->warn("Ada {$anomalies} barang dengan saldo minus! Perlu dicek.");
        } else {
            $this->info('Tidak ada anomali ditemukan.');
        }

        // Export to CSV
        if ($export) {
            $this->exportToCsv($rows);
        }

        return Command::SUCCESS;
    }

    private function exportToCsv(array $rows): void
    {
        $filename = storage_path('app/stok_verification_' . date('Y-m-d_His') . '.csv');
        
        $fp = fopen($filename, 'w');
        fputcsv($fp, ['Kode', 'Nama Barang', 'Masuk', 'Keluar', 'Saldo', 'Status']);
        
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        
        fclose($fp);
        
        $this->info("Exported to: {$filename}");
    }
}
