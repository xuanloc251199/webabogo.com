<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes to optimize product availability queries for large databases
     */
    public function up(): void
    {
        Schema::table('ec_product_variations', function (Blueprint $table) {
            // Index for finding rooms by hotel (configurable_product_id)
            if (!$this->indexExists('ec_product_variations', 'idx_configurable_product_id')) {
                $table->index(['configurable_product_id'], 'idx_configurable_product_id');
            }

            // Index for product_id lookup
            if (!$this->indexExists('ec_product_variations', 'idx_product_id')) {
                $table->index(['product_id'], 'idx_product_id');
            }
        });

        Schema::table('ec_products', function (Blueprint $table) {
            // Index for date range queries
            if (!$this->indexExists('ec_products', 'idx_date_range_status')) {
                $table->index(['start_date', 'end_date', 'status'], 'idx_date_range_status');
            }

            // Index for variation lookup with quantity
            if (!$this->indexExists('ec_products', 'idx_variation_quantity_status')) {
                $table->index(['is_variation', 'quantity', 'status'], 'idx_variation_quantity_status');
            }

            // Index for name search
            if (!$this->indexExists('ec_products', 'idx_name_status')) {
                $table->index(['name', 'status'], 'idx_name_status');
            }
        });

        Schema::table('placeables', function (Blueprint $table) {
            // Index for place filtering
            if (!$this->indexExists('placeables', 'idx_placeable_place')) {
                $table->index(['placeable_type', 'placeable_id', 'place_id'], 'idx_placeable_place');
            }
        });

        Schema::table('ec_product_category_product', function (Blueprint $table) {
            // Index for category filtering
            if (!$this->indexExists('ec_product_category_product', 'idx_category_product')) {
                $table->index(['category_id', 'product_id'], 'idx_category_product');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_product_variations', function (Blueprint $table) {
            $table->dropIndex('idx_configurable_product_id');
            $table->dropIndex('idx_product_id');
        });

        Schema::table('ec_products', function (Blueprint $table) {
            $table->dropIndex('idx_date_range_status');
            $table->dropIndex('idx_variation_quantity_status');
            $table->dropIndex('idx_name_status');
        });

        Schema::table('placeables', function (Blueprint $table) {
            $table->dropIndex('idx_placeable_place');
        });

        Schema::table('ec_product_category_product', function (Blueprint $table) {
            $table->dropIndex('idx_category_product');
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);

        return array_key_exists($indexName, $indexes);
    }
};
