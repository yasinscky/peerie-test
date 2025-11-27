<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'business_type')) {
                $table->dropColumn('business_type');
            }

            if (Schema::hasColumn('plans', 'business_niche')) {
                $table->dropColumn('business_niche');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'business_type')) {
                $table->dropColumn('business_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'business_niche')) {
                $table->string('business_niche')->nullable()->after('country');
            }

            if (!Schema::hasColumn('plans', 'business_type')) {
                $table->string('business_type')->nullable()->after('business_niche');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'business_type')) {
                $table->string('business_type')->nullable()->after('dependencies');
            }
        });
    }
};


