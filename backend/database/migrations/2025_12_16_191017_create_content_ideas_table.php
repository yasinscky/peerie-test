<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_ideas', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('title');
            $table->text('caption');
            $table->text('hashtags')->nullable();
            $table->text('tips')->nullable();
            $table->string('language', 2)->default('en')->index();
            $table->timestamps();
            $table->index(['date', 'language']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE content_ideas ADD CONSTRAINT content_ideas_language_check CHECK (language IN ('en','de'))");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('content_ideas') && DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE content_ideas DROP CONSTRAINT IF EXISTS content_ideas_language_check');
        }
        Schema::dropIfExists('content_ideas');
    }
};
