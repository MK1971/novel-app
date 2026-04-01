<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->timestamp('locked_at')->nullable()->after('is_locked');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('UPDATE chapters SET locked_at = updated_at WHERE is_locked = 1 AND locked_at IS NULL');
        } else {
            DB::table('chapters')
                ->where('is_locked', true)
                ->whereNull('locked_at')
                ->update(['locked_at' => DB::raw('updated_at')]);
        }
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('locked_at');
        });
    }
};
