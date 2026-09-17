<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('document_key')->nullable()->after('template');
            $table->string('document_group')->nullable()->after('document_key');
            $table->string('document_layout')->nullable()->after('document_group');
            $table->string('document_short_label')->nullable()->after('document_layout');
            $table->text('document_description')->nullable()->after('document_short_label');
            $table->json('document_fields')->nullable()->after('document_description');
            $table->index('document_key');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['document_key']);
            $table->dropColumn([
                'document_key',
                'document_group',
                'document_layout',
                'document_short_label',
                'document_description',
                'document_fields',
            ]);
        });
    }
};
