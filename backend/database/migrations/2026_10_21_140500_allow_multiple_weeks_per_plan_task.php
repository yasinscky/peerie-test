<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropUnique(['plan_id', 'task_id', 'year', 'month']);
            $table->unique(['plan_id', 'task_id', 'year', 'month', 'week']);
        });
    }

    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropUnique(['plan_id', 'task_id', 'year', 'month', 'week']);
            $table->unique(['plan_id', 'task_id', 'year', 'month']);
        });
    }
};
