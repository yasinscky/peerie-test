<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('preferred_week')->nullable()->after('frequency');
            $table->unsignedSmallInteger('eligibility_delay_months')->nullable()->after('preferred_week');
            $table->json('eligibility_after_action_ids')->nullable()->after('eligibility_delay_months');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_week',
                'eligibility_delay_months',
                'eligibility_after_action_ids',
            ]);
        });
    }
};
