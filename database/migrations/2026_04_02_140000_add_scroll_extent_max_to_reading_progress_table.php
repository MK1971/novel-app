<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('reading_progress', 'scroll_extent_max')) {
                $table->unsignedInteger('scroll_extent_max')->nullable()->after('scroll_position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->dropColumn('scroll_extent_max');
        });
    }
};
