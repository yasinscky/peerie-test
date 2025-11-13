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
        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->enum('industry', ['beauty', 'physio', 'coaching']);
            $table->enum('country', ['ie', 'uk', 'de']);
            $table->string('title'); // e.g. "Beauty Salon - IRL"
            $table->json('tags'); // ["#BeautySalonIreland", ...]
            $table->timestamps();
            $table->unique(['industry', 'country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtags');
    }
};
