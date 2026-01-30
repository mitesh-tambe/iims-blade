<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Facades\File;

class DbBackup extends Command
{
    protected $signature = 'db:backup {--days=7}';
    protected $description = 'Take MySQL database backup, zip it, and auto-delete old backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $db = config('database.connections.mysql');

        $date = Carbon::now()->format('Y-m-d_H-i-s');

        $backupDir = storage_path('app/db-backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $sqlFile = "{$db['database']}_{$date}.sql";
        $zipFile = "{$db['database']}_{$date}.zip";

        $sqlPath = "{$backupDir}/{$sqlFile}";
        $zipPath = "{$backupDir}/{$zipFile}";

        // 1️⃣ Dump database
        $dumpCommand = sprintf(
            '"C:\\xampp\\mysql\\bin\\mysqldump.exe" -h%s -u%s %s %s > "%s"',
            $db['host'],
            $db['username'],
            $db['password'] ? "-p{$db['password']}" : '',
            $db['database'],
            $sqlPath
        );

        exec($dumpCommand, $output, $result);

        if ($result !== 0) {
            $this->error('❌ Database dump failed');
            return Command::FAILURE;
        }

        // 2️⃣ Zip the SQL file
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $zip->addFile($sqlPath, $sqlFile);
            $zip->close();

            // Delete raw SQL after zip
            unlink($sqlPath);
        } else {
            $this->error('❌ Failed to create zip file');
            return Command::FAILURE;
        }

        $this->info("✅ Backup created: {$zipFile}");

        // 3️⃣ Auto-delete old backups
        $days = (int) $this->option('days');
        $this->deleteOldBackups($backupDir, $days);

        return Command::SUCCESS;
    }

    private function deleteOldBackups(string $dir, int $days): void
    {
        $files = File::files($dir);
        $cutoff = Carbon::now()->subDays($days);

        foreach ($files as $file) {
            if ($file->getExtension() === 'zip') {
                $lastModified = Carbon::createFromTimestamp($file->getMTime());
                if ($lastModified->lt($cutoff)) {
                    File::delete($file->getPathname());
                }
            }
        }

        $this->info("🧹 Old backups (>{$days} days) cleaned");
    }
}
