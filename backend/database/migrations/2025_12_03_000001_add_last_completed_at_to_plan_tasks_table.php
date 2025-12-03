<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->timestamp('last_completed_at')->nullable()->after('completed');
        });
    }

    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropColumn('last_completed_at');
        });
    }
};


