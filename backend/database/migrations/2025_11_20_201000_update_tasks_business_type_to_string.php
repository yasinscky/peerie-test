<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change business_type from enum to string so we can store industries like beauty/physio/coaching
        DB::statement("ALTER TABLE tasks ALTER COLUMN business_type TYPE VARCHAR(50) USING business_type::text");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot safely revert back to enum without losing non-enum values,
        // so we keep the column as string on rollback.
    }
};


