<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ec_products', function (Blueprint $table) {
            if (! Schema::hasColumn('ec_products', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('image');
            }
        });

        DB::table('ec_products')
            ->whereNull('thumbnail')
            ->update([
                'thumbnail' => DB::raw('image'),
            ]);
    }

    public function down(): void
    {
        Schema::table('ec_products', function (Blueprint $table) {
            if (Schema::hasColumn('ec_products', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }
};