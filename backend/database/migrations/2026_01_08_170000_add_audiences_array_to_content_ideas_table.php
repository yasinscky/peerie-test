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
            $table->jsonb('audiences')->nullable()->after('language');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE content_ideas SET audiences = jsonb_build_array(audience) WHERE audience IS NOT NULL AND (audiences IS NULL OR audiences = '[]'::jsonb)");
            DB::statement("CREATE INDEX IF NOT EXISTS content_ideas_audiences_gin ON content_ideas USING GIN (audiences)");
        }

        if (Schema::hasTable('content_ideas') && DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE content_ideas DROP CONSTRAINT IF EXISTS content_ideas_audience_check');
        }

        if (Schema::hasColumn('content_ideas', 'audience')) {
            Schema::table('content_ideas', function (Blueprint $table) {
                $table->dropIndex(['date', 'audience']);
                $table->dropIndex(['audience']);
                $table->dropColumn('audience');
            });
        }
    }

    public function down(): void
    {
        Schema::table('content_ideas', function (Blueprint $table) {
            $table->string('audience', 32)->nullable()->index()->after('language');
            $table->index(['date', 'audience']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE content_ideas SET audience = NULLIF(audiences->>0, '') WHERE audiences IS NOT NULL");
            DB::statement('DROP INDEX IF EXISTS content_ideas_audiences_gin');
            DB::statement("ALTER TABLE content_ideas ADD CONSTRAINT content_ideas_audience_check CHECK (audience IS NULL OR audience IN ('coaches_de','coaches_uk','coaches_ie','physio_de','physio_uk','physio_ie','beauty_de','beauty_uk','beauty_ie'))");
        }

        Schema::table('content_ideas', function (Blueprint $table) {
            $table->dropColumn('audiences');
        });
    }
};

