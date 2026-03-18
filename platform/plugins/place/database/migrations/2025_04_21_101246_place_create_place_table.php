<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 400)->nullable();
            $table->string('image')->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('places_translations', function (Blueprint $table) {
            $table->string('lang_code');
            $table->foreignId('places_id');
            $table->string('name', 255)->nullable();

            $table->primary(['lang_code', 'places_id'], 'places_translations_primary');
        });

        Schema::create('placeables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->index();
            $table->morphs('placeable');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('places');
        Schema::dropIfExists('places_translations');
        Schema::dropIfExists('placeables');
    }
};
