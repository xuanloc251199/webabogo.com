<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('image', 255)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
        Schema::create('product_extensions', function (Blueprint $table) {
            $table->foreignId('product_id')->index();
            $table->foreignId('extension_id')->index();
        });

        Schema::create('extensions_translations', function (Blueprint $table) {
            $table->string('lang_code');
            $table->foreignId('extensions_id');
            $table->string('name', 255)->nullable();

            $table->primary(['lang_code', 'extensions_id'], 'extensions_translations_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_extensions');
        Schema::dropIfExists('extensions');
        Schema::dropIfExists('extensions_translations');
    }
};
