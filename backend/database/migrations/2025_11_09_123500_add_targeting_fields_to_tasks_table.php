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
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('global_order')->nullable()->after('external_id');
            $table->boolean('is_global')->default(false)->after('global_order');
            $table->json('target_countries')->nullable()->after('is_global');
            $table->json('target_industries')->nullable()->after('target_countries');
            $table->json('allowed_capacities')->nullable()->after('target_industries');
            $table->json('local_presence_options')->nullable()->after('allowed_capacities');
            $table->json('conditions')->nullable()->after('local_presence_options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'global_order',
                'is_global',
                'target_countries',
                'target_industries',
                'allowed_capacities',
                'local_presence_options',
                'conditions',
            ]);
        });
    }
};

