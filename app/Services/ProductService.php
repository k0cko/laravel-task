<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getAll(array $params)
    {
        return Product::with('categories')
            ->when(filled($params['price_from'] ?? null), function ($query) use ($params) {
                $query->where('price', '>=', $params['price_from']);
            })
            ->when(filled($params['price_to'] ?? null), function ($query) use ($params) {
                $query->where('price', '<=', $params['price_to']);
            })
            ->when(filled($params['search'] ?? null), function ($query) use ($params) {
                $query->whereFullText(['title', 'content'], $params['search']);
            })
            ->simplePaginate(25);
    }
}