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
        Schema::create('chapter_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('total_reads')->default(0);
            $table->integer('total_edits')->default(0);
            $table->integer('accepted_edits')->default(0);
            $table->integer('total_votes')->default(0);
            $table->integer('total_reactions')->default(0);
            $table->float('average_rating')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_statistics');
    }
};
