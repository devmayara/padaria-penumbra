<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            [
                'name' => 'Pão Francês',
                'category' => 'Pães',
                'unit_price' => 1.5, // R$ 1,50
                'current_quantity' => 100,
            ],
            [
                'name' => 'Pão de Queijo',
                'category' => 'Pães',
                'unit_price' => 3, // R$ 3,00
                'current_quantity' => 50,
            ],
            [
                'name' => 'Croissant',
                'category' => 'Pães',
                'unit_price' => 4.5, // R$ 4,50
                'current_quantity' => 30,
            ],
            [
                'name' => 'X-Burger',
                'category' => 'Lanches',
                'unit_price' => 12, // R$ 12,00
                'current_quantity' => 25,
            ],
            [
                'name' => 'X-Salada',
                'category' => 'Lanches',
                'unit_price' => 15, // R$ 15,00
                'current_quantity' => 20,
            ],
            [
                'name' => 'Refrigerante',
                'category' => 'Alimentos',
                'unit_price' => 5, // R$ 5,00
                'current_quantity' => 60,
            ],
            [
                'name' => 'Suco Natural',
                'category' => 'Alimentos',
                'unit_price' => 6, // R$ 6,00
                'current_quantity' => 40,
            ],
        ];

        foreach ($products as $productData) {
            $category = $categories->where('name', $productData['category'])->first();

            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'unit_price' => $productData['unit_price'],
                    'current_quantity' => $productData['current_quantity'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
