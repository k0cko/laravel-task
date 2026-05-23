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
        $categories = Category::all();
        for ($i = 0; $i < 100; $i++) {
            $products = Product::factory(500)->create();
            $pivotData = [];

            foreach ($products as $product) {
                $randomCategoryIds = $categories->random(random_int(1, 3))->pluck('id');

                foreach ($randomCategoryIds as $categoryId) {
                    $pivotData[] = [
                        'product_id' => $product->id,
                        'category_id' => $categoryId,
                    ];
                }
            }

            DB::table('category_product')->insert($pivotData);
        }
    }
}
