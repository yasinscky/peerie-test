<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_files', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('language', 2)->default('en')->index();
            $table->string('file_path');
            $table->string('original_filename');
            $table->timestamps();
            $table->index(['language', 'created_at']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE resource_files ADD CONSTRAINT resource_files_language_check CHECK (language IN ('en','de'))");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('resource_files') && DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE resource_files DROP CONSTRAINT IF EXISTS resource_files_language_check');
        }
        Schema::dropIfExists('resource_files');
    }
};
