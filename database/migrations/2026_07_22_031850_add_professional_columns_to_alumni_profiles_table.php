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
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('phone_number');
            $table->text('about_me')->nullable()->after('avatar_url');
            $table->json('skills')->nullable()->after('about_me');
            $table->string('linkedin_url')->nullable()->after('skills');
            $table->string('portfolio_url')->nullable()->after('linkedin_url');
            $table->string('resume_url')->nullable()->after('portfolio_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_url',
                'about_me',
                'skills',
                'linkedin_url',
                'portfolio_url',
                'resume_url'
            ]);
        });
    }
};
