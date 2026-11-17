<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('document_image')->nullable()->after('document_description');
        });

        $covers = [
            'buyer-persona' => 'buyer-persona.webp',
            'brand-identity' => 'brand-identity.webp',
            'content-audit' => 'content-audit.webp',
            'content-bank' => 'content-bank.webp',
            'content-planning' => 'content-planning.webp',
        ];

        foreach ($covers as $key => $file) {
            DB::table('tasks')->where('document_key', $key)->update(['document_image' => $file]);
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('document_image');
        });
    }
};
