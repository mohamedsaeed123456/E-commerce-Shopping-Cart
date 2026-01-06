<?php

namespace App\Services\Product;

use App\Jobs\NotifyLowStock;
use App\Models\Product;

class ProductService
{
    /**
     * Get all products
     */
    public function getAllProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::all();
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        $product = Product::create($data);
        return $product;
    }
}
