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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('country');
            $table->string('business_niche');
            $table->string('language');
            $table->boolean('is_local_business');
            $table->boolean('has_website');
            $table->integer('marketing_time_per_week'); // Часов в неделю на маркетинг
            $table->json('questionnaire_data')->nullable(); // Полные данные анкеты
            $table->timestamps();

            // Индексы
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
