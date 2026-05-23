<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id');
        
        $totalProducts = 500000;
        $chunkSize = 5000;
        $now = now();

        $lastProductId = (Product::max('id') ?? 0) + 1;

        for ($i = 0; $i < ($totalProducts / $chunkSize); $i++) {
            $productsData = Product::factory($chunkSize)->raw([
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            Product::insert($productsData);

            $pivotData = [];
            foreach ($productsData as $product) {
                $randomCategoryIds = $categoryIds->random(random_int(1, 3));
                
                foreach ($randomCategoryIds as $categoryId) {
                    $pivotData[] = [
                        'product_id' => $lastProductId,
                        'category_id' => $categoryId,
                    ];
                }

                $lastProductId++;
            }

            DB::table('category_product')->insert($pivotData);
        }
    }
}
