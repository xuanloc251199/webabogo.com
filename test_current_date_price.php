<?php

require_once 'bootstrap/app.php';

use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;

// Test helper function
if (function_exists('get_current_date_price')) {
    echo "Helper function exists!\n";
    
    // Get first product
    $product = Product::first();
    if ($product) {
        echo "Testing with product: " . $product->name . "\n";
        echo "Regular price: " . ($product->sale_price ?: $product->price) . "\n";
        
        $currentDatePrice = get_current_date_price($product);
        echo "Current date price: " . $currentDatePrice . "\n";
        echo "Current date: " . Carbon::now()->toDateString() . "\n";
    } else {
        echo "No products found in database\n";
    }
} else {
    echo "Helper function does not exist!\n";
}
