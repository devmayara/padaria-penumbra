<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'unit_price' => 150, // R$ 1,50
                'current_quantity' => 100,
            ],
            [
                'name' => 'Pão de Queijo',
                'category' => 'Pães',
                'unit_price' => 300, // R$ 3,00
                'current_quantity' => 50,
            ],
            [
                'name' => 'Croissant',
                'category' => 'Pães',
                'unit_price' => 450, // R$ 4,50
                'current_quantity' => 30,
            ],
            [
                'name' => 'X-Burger',
                'category' => 'Lanches',
                'unit_price' => 1200, // R$ 12,00
                'current_quantity' => 25,
            ],
            [
                'name' => 'X-Salada',
                'category' => 'Lanches',
                'unit_price' => 1500, // R$ 15,00
                'current_quantity' => 20,
            ],
            [
                'name' => 'Refrigerante',
                'category' => 'Alimentos',
                'unit_price' => 500, // R$ 5,00
                'current_quantity' => 60,
            ],
            [
                'name' => 'Suco Natural',
                'category' => 'Alimentos',
                'unit_price' => 600, // R$ 6,00
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
