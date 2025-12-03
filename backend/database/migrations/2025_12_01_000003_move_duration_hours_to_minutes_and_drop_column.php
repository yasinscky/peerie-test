<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tasks', 'duration_hours')) {
            DB::table('tasks')
                ->whereNull('duration_minutes')
                ->whereNotNull('duration_hours')
                ->update([
                    'duration_minutes' => DB::raw('duration_hours * 60'),
                ]);

            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('duration_hours');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'duration_hours')) {
                $table->integer('duration_hours')
                    ->nullable()
                    ->after('description');
            }
        });

        DB::table('tasks')
            ->whereNull('duration_hours')
            ->whereNotNull('duration_minutes')
            ->update([
                'duration_hours' => DB::raw('CEIL(duration_minutes::numeric / 60)'),
            ]);
    }
};


