<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_ideas', function (Blueprint $table) {
            $table->string('audience', 32)->nullable()->index()->after('language');
            $table->index(['date', 'audience']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE content_ideas ADD CONSTRAINT content_ideas_audience_check CHECK (audience IS NULL OR audience IN ('coaches_de','coaches_uk','coaches_ie','physio_de','physio_uk','physio_ie','beauty_de','beauty_uk','beauty_ie'))");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('content_ideas') && DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE content_ideas DROP CONSTRAINT IF EXISTS content_ideas_audience_check');
        }

        Schema::table('content_ideas', function (Blueprint $table) {
            $table->dropIndex(['date', 'audience']);
            $table->dropIndex(['audience']);
            $table->dropColumn('audience');
        });
    }
};

