<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ec_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer("customer_id")->nullable();
            $table->enum("action", ["promotion", "purchase", "payment", "account"]);
            $table->string("title")->nullable();
            $table->string("content")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_notifications');
    }
};
