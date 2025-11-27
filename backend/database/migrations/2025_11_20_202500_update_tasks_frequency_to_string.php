<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('frequency', 50)->default('once')->change();
        });
    }

    public function down(): void
    {
        // No reliable way to restore the original enum type
    }
};


