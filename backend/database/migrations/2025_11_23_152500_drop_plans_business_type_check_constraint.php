<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE plans DROP CONSTRAINT IF EXISTS plans_business_type_check');
    }

    public function down(): void
    {
    }
};


