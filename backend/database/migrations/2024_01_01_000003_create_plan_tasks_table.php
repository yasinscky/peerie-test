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
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->integer('week'); // Week number (1-4)
            $table->boolean('completed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique index: one task cannot appear twice in the same plan and week
            $table->unique(['plan_id', 'task_id', 'week']);
            
            // Indexes for faster querying
            $table->index(['plan_id', 'week']);
            $table->index('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
