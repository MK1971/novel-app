<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false);
            $table->integer('round_number')->nullable();
            $table->string('version_label')->nullable(); // e.g., "Version A", "Version B"
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'round_number', 'version_label']);
        });
    }
};
