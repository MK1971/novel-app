<?php

namespace App\Console\Commands;

use Database\Seeders\AdminOnlySeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetAppDataCommand extends Command
{
    protected $signature = 'db:reset-app-data
        {--force : Truncate all application data without confirmation}';

    protected $description = 'Truncate all tables except migrations, then seed only the admin user';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This deletes ALL application data. Continue?')) {
            $this->components->info('Aborted.');

            return self::SUCCESS;
        }

        $tables = $this->applicationTableNames();

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->getLaravel()->make(AdminOnlySeeder::class)->run();

        $email = (string) config('app.admin_email', 'admin@example.com');
        $this->components->info("Done. Login: {$email} / password");

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function applicationTableNames(): array
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
            default => throw new \RuntimeException("db:reset-app-data does not support driver [{$driver}]."),
        };

        $skip = ['migrations', 'sqlite_sequence'];

        return collect($names)
            ->reject(fn (string $name) => in_array($name, $skip, true))
            ->values()
            ->all();
    }
}
