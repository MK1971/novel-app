<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('edits', function (Blueprint $table) {
            $table->boolean('show_in_public_feed')->default(true)->after('status');
        });

        Schema::table('inline_edits', function (Blueprint $table) {
            $table->boolean('show_in_public_feed')->default(true)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edits', function (Blueprint $table) {
            $table->dropColumn('show_in_public_feed');
        });

        Schema::table('inline_edits', function (Blueprint $table) {
            $table->dropColumn('show_in_public_feed');
        });
    }
};
