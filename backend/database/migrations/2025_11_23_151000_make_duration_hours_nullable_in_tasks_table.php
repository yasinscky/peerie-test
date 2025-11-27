<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tasks ALTER COLUMN duration_hours DROP NOT NULL');
    }

    public function down(): void
    {
    }
};


