<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getAll(array $params)
    {
        $products = Product::with('categories');

        if (!empty($params['price_from'])) {
            $products->where('price', '>=', $params['price_from']);
        }
        
        if (!empty($params['price_to'])) {
            $products->where('price', '<=', $params['price_to']);
        }

        if (!empty($params['search'])) {
            $products->whereFullText(['title', 'content'], $params['search']);
        }

        return $products->paginate(25);
    }
}