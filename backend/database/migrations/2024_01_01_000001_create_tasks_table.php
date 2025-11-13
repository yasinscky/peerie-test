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
            $table->integer('duration_hours'); // Длительность в часах
            $table->enum('frequency', ['once', 'weekly', 'monthly', 'quarterly']); // Частота выполнения
            $table->json('dependencies')->nullable(); // JSON массив ID зависимых задач
            $table->enum('business_type', ['any', 'ecommerce', 'service', 'saas', 'content']); // Тип бизнеса
            $table->string('language')->default('ru'); // Язык
            $table->boolean('is_local')->default(false); // Локальный бизнес
            $table->boolean('requires_website')->default(false); // Требует наличие сайта
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']); // Уровень сложности
            $table->string('category'); // Категория задачи
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index(['business_type', 'language', 'is_local', 'requires_website']);
            $table->index('category');
            $table->index('difficulty_level');
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
