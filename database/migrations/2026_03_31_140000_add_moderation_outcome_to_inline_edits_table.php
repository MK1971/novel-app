<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inline_edits', function (Blueprint $table) {
            $table->string('moderation_outcome', 16)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('inline_edits', function (Blueprint $table) {
            $table->dropColumn('moderation_outcome');
        });
    }
};
