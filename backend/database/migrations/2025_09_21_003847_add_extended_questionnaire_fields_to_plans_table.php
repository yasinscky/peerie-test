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
        Schema::table('plans', function (Blueprint $table) {
            // Extended questionnaire fields
            $table->json('industries')->nullable()->after('questionnaire_data');
            $table->boolean('business_goals_defined')->default(false)->after('industries');
            $table->boolean('marketing_goals_defined')->default(false)->after('business_goals_defined');
            $table->boolean('google_business_claimed')->default(false)->after('marketing_goals_defined');
            $table->boolean('core_directories_claimed')->default(false)->after('google_business_claimed');
            $table->boolean('industry_directories_claimed')->default(false)->after('core_directories_claimed');
            $table->boolean('business_directories_claimed')->default(false)->after('industry_directories_claimed');
            $table->boolean('email_marketing_tool')->default(false)->after('business_directories_claimed');
            $table->boolean('crm_pipeline')->default(false)->after('email_marketing_tool');
            $table->string('running_ads')->default('none')->after('crm_pipeline');
            $table->boolean('has_primary_social_channel')->default(false)->after('running_ads');
            $table->string('primary_social_channel')->nullable()->after('has_primary_social_channel');
            $table->boolean('has_secondary_social_channel')->default(false)->after('primary_social_channel');
            $table->string('secondary_social_channel')->nullable()->after('has_secondary_social_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'industries',
                'business_goals_defined',
                'marketing_goals_defined',
                'google_business_claimed',
                'core_directories_claimed',
                'industry_directories_claimed',
                'business_directories_claimed',
                'email_marketing_tool',
                'crm_pipeline',
                'running_ads',
                'has_primary_social_channel',
                'primary_social_channel',
                'has_secondary_social_channel',
                'secondary_social_channel'
            ]);
        });
    }
};
