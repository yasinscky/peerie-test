<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hashtags', function (Blueprint $table) {
            $table->dropUnique(['industry', 'country']);
            
            $table->enum('language', ['en', 'de'])->default('en')->after('country');
            
            $table->unique(['industry', 'country', 'language']);
        });
    }

    public function down(): void
    {
        Schema::table('hashtags', function (Blueprint $table) {
            $table->dropUnique(['industry', 'country', 'language']);
            
            $table->dropColumn('language');
            
            $table->unique(['industry', 'country']);
        });
    }
};
