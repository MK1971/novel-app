<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('paid_at')->constrained()->nullOnDelete();
            $table->unique('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });
    }
};
