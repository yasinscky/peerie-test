<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hashtags', function (Blueprint $table) {
            // Drop existing unique constraint
            $table->dropUnique(['industry', 'country']);
            
            // Add language column
            $table->enum('language', ['en', 'de'])->default('en')->after('country');
            
            // Add new unique constraint with language
            $table->unique(['industry', 'country', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hashtags', function (Blueprint $table) {
            // Drop new unique constraint
            $table->dropUnique(['industry', 'country', 'language']);
            
            // Remove language column
            $table->dropColumn('language');
            
            // Restore old unique constraint
            $table->unique(['industry', 'country']);
        });
    }
};
