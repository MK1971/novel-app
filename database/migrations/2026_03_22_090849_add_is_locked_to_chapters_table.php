<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $blueprint) {
            $blueprint->boolean('is_locked')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $blueprint) {
            $blueprint->dropColumn('is_locked');
        });
    }
};
