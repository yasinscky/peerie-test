<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resource_files', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->index(['language', 'sort_order', 'published_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('resource_files', function (Blueprint $table) {
            $table->dropIndex(['language', 'sort_order', 'published_at', 'created_at']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['sort_order']);
            $table->dropColumn(['sort_order', 'published_at']);
        });
    }
};
