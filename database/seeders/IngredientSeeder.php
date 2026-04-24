<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Vöi (Buns/Bread)
            ['name' => 'Seesami sai', 'category' => 'vöi', 'price' => 0.50],
            ['name' => 'Brioche kukkel', 'category' => 'vöi', 'price' => 0.75],
            ['name' => 'Riisikukkel', 'category' => 'vöi', 'price' => 0.60],
            
            // Pitav (Proteins/Meats)
            ['name' => 'Veise', 'category' => 'pitav', 'price' => 2.50],
            ['name' => 'Kana', 'category' => 'pitav', 'price' => 2.00],
            ['name' => 'Sealiha', 'category' => 'pitav', 'price' => 2.30],
            ['name' => 'Vegan pihv', 'category' => 'pitav', 'price' => 2.20],
            ['name' => 'Grill pihv', 'category' => 'pitav', 'price' => 2.80],
            ['name' => 'Tavaline pihv', 'category' => 'pitav', 'price' => 2.40],
            
            // Juust (Cheese)
            ['name' => 'Cheddar', 'category' => 'juust', 'price' => 0.50],
            ['name' => 'Mozzarella', 'category' => 'juust', 'price' => 0.60],
            ['name' => 'Blue cheese', 'category' => 'juust', 'price' => 0.70],
            
            // Salat (Salads/Vegetables)
            ['name' => 'Salat', 'category' => 'salat', 'price' => 0.30],
            ['name' => 'Tomat', 'category' => 'salat', 'price' => 0.30],
            ['name' => 'Kurk', 'category' => 'salat', 'price' => 0.25],
            ['name' => 'Sibul', 'category' => 'salat', 'price' => 0.20],
            ['name' => 'Avocado', 'category' => 'salat', 'price' => 0.80],
            ['name' => 'Kastrullsibul', 'category' => 'salat', 'price' => 0.40],
            
            // Lisand (Extras/Sauces)
            ['name' => 'Ketšup', 'category' => 'lisand', 'price' => 0.20],
            ['name' => 'Majonees', 'category' => 'lisand', 'price' => 0.20],
            ['name' => 'BBQ kaste', 'category' => 'lisand', 'price' => 0.30],
            ['name' => 'Chipotle kaste', 'category' => 'lisand', 'price' => 0.35],
            ['name' => 'Peekon', 'category' => 'lisand', 'price' => 0.60],
            ['name' => 'Muna', 'category' => 'lisand', 'price' => 0.50],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate(
                ['name' => $ingredient['name'], 'category' => $ingredient['category']],
                [
                    'price' => $ingredient['price'],
                    'is_available' => true,
                ]
            );
        }
    }
}