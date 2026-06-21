<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Process\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:backup {--keep=14 : Number of most recent backups to retain}', function () {
    $connectionName = config('database.default');
    $connection = config("database.connections.{$connectionName}");

    if (($connection['driver'] ?? null) !== 'mysql') {
        $this->error('db:backup currently supports only MySQL connections.');

        return self::FAILURE;
    }

    $backupDirectory = storage_path('app/backups/database');

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0755, true) && !is_dir($backupDirectory)) {
        $this->error("Unable to create backup directory: {$backupDirectory}");

        return self::FAILURE;
    }

    $database = (string) ($connection['database'] ?? '');
    $host = (string) ($connection['host'] ?? '127.0.0.1');
    $port = (string) ($connection['port'] ?? '3306');
    $username = (string) ($connection['username'] ?? '');
    $password = (string) ($connection['password'] ?? '');

    if ($database === '' || $username === '') {
        $this->error('Database name and username are required for backups.');

        return self::FAILURE;
    }

    $timestamp = now()->format('Ymd_His');
    $filename = "{$database}_{$timestamp}.sql";
    $fullPath = $backupDirectory . DIRECTORY_SEPARATOR . $filename;

    $command = [
        'mysqldump',
        "--host={$host}",
        "--port={$port}",
        "--user={$username}",
        '--single-transaction',
        '--quick',
        '--routines',
        '--triggers',
        '--events',
        $database,
    ];

    $process = new Process($command);
    $process->setTimeout(300);
    $process->setEnv(['MYSQL_PWD' => $password]);
    $process->run();

    if (!$process->isSuccessful()) {
        $this->error('Database backup failed.');
        $this->line(trim($process->getErrorOutput()) ?: trim($process->getOutput()));

        return self::FAILURE;
    }

    if (file_put_contents($fullPath, $process->getOutput()) === false) {
        $this->error("Backup process succeeded but writing file failed: {$fullPath}");

        return self::FAILURE;
    }

    $keep = max((int) $this->option('keep'), 1);
    $files = glob($backupDirectory . DIRECTORY_SEPARATOR . '*.sql') ?: [];
    rsort($files);

    foreach (array_slice($files, $keep) as $oldFile) {
        @unlink($oldFile);
    }

    $this->info("Backup created: {$fullPath}");
    $this->info("Retention applied: keeping latest {$keep} backup(s)");

    return self::SUCCESS;
})->purpose('Create a timestamped MySQL backup and rotate old dumps');

Schedule::command('db:backup --keep=14')->dailyAt('01:30');
