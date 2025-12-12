<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->integer('year')->nullable()->after('week');
            $table->integer('month')->nullable()->after('year');
        });

        $now = Carbon::now();
        \DB::table('plan_tasks')->update([
            'year' => $now->year,
            'month' => $now->month,
        ]);

        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->integer('year')->nullable(false)->change();
            $table->integer('month')->nullable(false)->change();
        });

        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropUnique(['plan_id', 'task_id', 'week']);
            $table->unique(['plan_id', 'task_id', 'year', 'month']);
            $table->index(['plan_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropUnique(['plan_id', 'task_id', 'year', 'month']);
            $table->dropIndex(['plan_id', 'year', 'month']);
            $table->unique(['plan_id', 'task_id', 'week']);
        });

        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropColumn(['year', 'month']);
        });
    }
};
