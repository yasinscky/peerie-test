<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tasks', 'external_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropUnique('tasks_external_id_unique');
                $table->dropColumn('external_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'external_id')) {
                $table->string('external_id')->nullable()->unique()->after('id');
            }
        });
    }
};


