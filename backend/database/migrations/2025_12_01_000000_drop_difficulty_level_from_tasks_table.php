<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'difficulty_level')) {
                $table->dropColumn('difficulty_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'difficulty_level')) {
                $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])
                    ->nullable()
                    ->after('requires_website');
                $table->index('difficulty_level');
            }
        });
    }
};


