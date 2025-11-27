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
        DB::statement("ALTER TABLE tasks ALTER COLUMN local_presence_options TYPE text USING CASE 
            WHEN local_presence_options IS NULL THEN NULL 
            WHEN local_presence_options::text LIKE '[%' THEN (local_presence_options::json->>0)::text 
            ELSE local_presence_options::text 
        END");
        
        DB::statement("ALTER TABLE tasks ALTER COLUMN template TYPE text USING CASE 
            WHEN template IS NULL THEN NULL 
            WHEN template::text LIKE '[%' THEN (template::json->>0)::text 
            ELSE template::text 
        END");
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('local_presence_options', 10)->nullable()->change();
            $table->string('template', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->json('local_presence_options')->nullable()->change();
            $table->json('template')->nullable()->change();
        });
    }
};
