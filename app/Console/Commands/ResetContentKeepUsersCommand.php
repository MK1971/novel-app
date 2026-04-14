<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetContentKeepUsersCommand extends Command
{
    protected $signature = 'db:reset-content-keep-users
        {--force : Truncate app content tables without confirmation}';

    protected $description = 'Truncate application data while preserving users and migrations';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This deletes app content but keeps users. Continue?')) {
            $this->components->info('Aborted.');

            return self::SUCCESS;
        }

        $tables = $this->contentTableNames();

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->components->info('Done. Content cleared, users preserved.');

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function contentTableNames(): array
    {
        $driver = DB::getDriverName();

        $names = match ($driver) {
            'sqlite' => collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"))
                ->pluck('name')
                ->all(),
            'mysql', 'mariadb' => collect(DB::select(
                'SELECT TABLE_NAME AS name FROM information_schema.tables WHERE table_schema = ? AND TABLE_TYPE = \'BASE TABLE\'',
                [DB::getDatabaseName()]
            ))
                ->pluck('name')
                ->all(),
            'pgsql' => collect(DB::select("SELECT tablename AS name FROM pg_tables WHERE schemaname = 'public'"))
                ->pluck('name')
                ->all(),
            default => throw new \RuntimeException("db:reset-content-keep-users does not support driver [{$driver}]."),
        };

        $skip = ['migrations', 'users', 'sqlite_sequence'];

        return collect($names)
            ->reject(fn (string $name) => in_array($name, $skip, true))
            ->values()
            ->all();
    }
}

