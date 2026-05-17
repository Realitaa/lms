<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('lessons', 'slug')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }

        // Generate slugs for existing lessons that don't have one
        $lessons = \App\Models\Lesson::whereNull('slug')->orWhere('slug', '')->get();
        foreach ($lessons as $lesson) {
            $lesson->slug = Str::slug($lesson->title) . '-' . $lesson->id;
            $lesson->save();
        }
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
