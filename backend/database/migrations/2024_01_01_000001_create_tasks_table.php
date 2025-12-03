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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('duration_minutes'); // Duration in minutes
            $table->enum('frequency', ['once', 'weekly', 'monthly', 'quarterly']); // Execution frequency
            $table->json('dependencies')->nullable(); // JSON array of dependent task IDs
            $table->string('language')->default('en'); // Task language
            $table->boolean('is_local')->default(false); // Local business only
            $table->boolean('requires_website')->default(false); // Requires website to be present
            $table->string('category'); // Task category
            $table->timestamps();

            // Indexes for faster querying
            $table->index(['language', 'is_local', 'requires_website']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
