<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('image', 255)->nullable();
            $table->integer('price')->default(0);
            $table->text('description')->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('product_service', function (Blueprint $table) {
            $table->foreignId('product_id')->index();
            $table->foreignId('service_id')->index();
        });
        Schema::create('services_translations', function (Blueprint $table) {
            $table->string('lang_code');
            $table->foreignId('services_id');
            $table->string('name', 255)->nullable();
            $table->primary(['lang_code', 'services_id'], 'services_translations_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_service');
        Schema::dropIfExists('services');
        Schema::dropIfExists('services_translations');
    }
};
