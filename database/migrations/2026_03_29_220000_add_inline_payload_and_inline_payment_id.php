<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edits', function (Blueprint $table) {
            $table->text('inline_edit_payload')->nullable()->after('edited_text');
        });

        Schema::table('inline_edits', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('status')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inline_edits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });

        Schema::table('edits', function (Blueprint $table) {
            $table->dropColumn('inline_edit_payload');
        });
    }
};
