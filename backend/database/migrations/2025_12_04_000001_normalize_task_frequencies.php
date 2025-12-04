<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tasks')
            ->where('frequency', 'half-yearly')
            ->update(['frequency' => 'half_yearly']);

        DB::table('tasks')
            ->where('frequency', 'biweekly')
            ->update(['frequency' => 'bi_weekly']);
    }

    public function down(): void
    {
        // No reliable way to restore original string values
    }
};


