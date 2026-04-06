<?php

namespace Database\Seeders;

use App\Models\AddonItem;
use Illuminate\Database\Seeder;

class AddonItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Sizes
            ['type' => 'size', 'name' => 'Väike',    'price' => 0.00, 'slug' => 'small',  'sort_order' => 1],
            ['type' => 'size', 'name' => 'Keskmine',  'price' => 1.50, 'slug' => 'medium', 'sort_order' => 2],
            ['type' => 'size', 'name' => 'Suur',      'price' => 3.00, 'slug' => 'large',  'sort_order' => 3],

            // Drinks
            ['type' => 'drink', 'name' => 'Coca-Cola 0.5L',      'price' => 2.50, 'slug' => 'coca-cola',      'sort_order' => 1],
            ['type' => 'drink', 'name' => 'Coca-Cola Zero 0.5L', 'price' => 2.50, 'slug' => 'coca-cola-zero', 'sort_order' => 2],
            ['type' => 'drink', 'name' => 'Fanta 0.5L',          'price' => 2.50, 'slug' => 'fanta',          'sort_order' => 3],
            ['type' => 'drink', 'name' => 'Sprite 0.5L',         'price' => 2.50, 'slug' => 'sprite',         'sort_order' => 4],
            ['type' => 'drink', 'name' => 'Vesi 0.5L',           'price' => 1.50, 'slug' => 'vesi',           'sort_order' => 5],

            // Sauces
            ['type' => 'sauce', 'name' => 'Ketchup',                  'price' => 0.00, 'slug' => 'ketchup',     'sort_order' => 1],
            ['type' => 'sauce', 'name' => 'Majonees',                  'price' => 0.00, 'slug' => 'majonees',    'sort_order' => 2],
            ['type' => 'sauce', 'name' => 'BBQ kaste',                 'price' => 0.50, 'slug' => 'bbq',         'sort_order' => 3],
            ['type' => 'sauce', 'name' => 'Sinihallitusjuustu kaste',  'price' => 0.50, 'slug' => 'blue-cheese', 'sort_order' => 4],
            ['type' => 'sauce', 'name' => 'Küüslaugukaste',            'price' => 0.50, 'slug' => 'garlic',      'sort_order' => 5],
            ['type' => 'sauce', 'name' => 'Chili kaste',               'price' => 0.50, 'slug' => 'chili',       'sort_order' => 6],

            // Fries
            ['type' => 'fries', 'name' => 'Ei soovi',        'price' => 0.00, 'slug' => 'none',  'sort_order' => 1],
            ['type' => 'fries', 'name' => 'Väike friikartul', 'price' => 2.00, 'slug' => 'small', 'sort_order' => 2],
            ['type' => 'fries', 'name' => 'Suur friikartul',  'price' => 3.50, 'slug' => 'large', 'sort_order' => 3],
        ];

        foreach ($items as $item) {
            AddonItem::firstOrCreate(
                ['type' => $item['type'], 'slug' => $item['slug']],
                $item
            );
        }
    }
}
