<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-except-essential';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan semua tabel kecuali tabel untuk dashboard beranda dan order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tabel yang TIDAK boleh dikosongkan (untuk dashboard beranda dan order)
        $protectedTables = [
            'users',
            'shifts',
            'orders',
            'order_items',
            'menus',
            'category_menus',
            'meja_billiards',
            'hero_sections',
            'tentang_kamis',
            'keunggulan_fasilitas',
            'events',
            'footers',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
        ];

        // Dapatkan semua tabel di database
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";

        $this->info('Memulai proses pengosongan tabel...');
        $this->newLine();

        $truncatedCount = 0;
        $skippedCount = 0;

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Skip tabel yang dilindungi
            if (in_array($tableName, $protectedTables)) {
                $this->line("⏭️  Skip: {$tableName} (dilindungi)");
                $skippedCount++;

                continue;
            }

            try {
                // Disable foreign key checks sementara
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                // Truncate tabel
                DB::table($tableName)->truncate();

                // Enable kembali foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                $this->info("✅ Dikosongkan: {$tableName}");
                $truncatedCount++;
            } catch (\Exception $e) {
                $this->error("❌ Error pada {$tableName}: ".$e->getMessage());
            }
        }

        $this->newLine();
        $this->info('Selesai!');
        $this->line("Tabel yang dikosongkan: {$truncatedCount}");
        $this->line("Tabel yang dilindungi: {$skippedCount}");

        return Command::SUCCESS;
    }
}
