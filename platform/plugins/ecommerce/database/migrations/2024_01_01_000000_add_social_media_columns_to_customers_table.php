<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ec_customers', function (Blueprint $table) {
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ec_customers', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'instagram_url', 
                'youtube_url',
                'tiktok_url',
                'twitter_url',
                'linkedin_url'
            ]);
        });
    }
};
