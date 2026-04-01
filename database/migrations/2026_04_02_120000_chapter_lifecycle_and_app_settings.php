<?php

use App\Models\Chapter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('updated_at');
            $table->timestamp('editing_closes_at')->nullable()->after('published_at');
            $table->boolean('is_reader_archive_link')->default(false)->after('is_archived');
            $table->timestamp('editing_deadline_reminder_sent_at')->nullable()->after('editing_closes_at');
        });

        foreach (Chapter::query()->whereNull('published_at')->cursor() as $chapter) {
            $chapter->published_at = $chapter->created_at;
            $chapter->editing_closes_at = $chapter->created_at->copy()->addDays(30);
            $chapter->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn([
                'published_at',
                'editing_closes_at',
                'is_reader_archive_link',
                'editing_deadline_reminder_sent_at',
            ]);
        });

        Schema::dropIfExists('app_settings');
    }
};
