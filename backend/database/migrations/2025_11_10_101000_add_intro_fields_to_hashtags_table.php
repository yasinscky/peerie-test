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
            if (!Schema::hasColumn('hashtags', 'intro_title')) {
                $table->string('intro_title')->nullable()->after('title');
            }

            if (!Schema::hasColumn('hashtags', 'intro_description')) {
                $table->text('intro_description')->nullable()->after('intro_title');
            }

            if (!Schema::hasColumn('hashtags', 'guidelines')) {
                $table->json('guidelines')->nullable()->after('intro_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hashtags', function (Blueprint $table) {
            if (Schema::hasColumn('hashtags', 'guidelines')) {
                $table->dropColumn('guidelines');
            }

            if (Schema::hasColumn('hashtags', 'intro_description')) {
                $table->dropColumn('intro_description');
            }

            if (Schema::hasColumn('hashtags', 'intro_title')) {
                $table->dropColumn('intro_title');
            }
        });
    }
};


