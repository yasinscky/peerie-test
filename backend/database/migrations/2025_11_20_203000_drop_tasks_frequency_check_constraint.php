<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old Postgres check constraint created for enum frequency
        DB::statement('ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_frequency_check');
    }

    public function down(): void
    {
        // No-op: original enum constraint is not restored
    }
};


