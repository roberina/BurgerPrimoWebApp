<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'ERIPAKKUMISED', 'slug' => 'eripakkumised', 'description' => 'Parimad pakkumised ja allahindlused', 'sort_order' => 0, 'is_active' => true],
            ['name' => 'BURGERID',      'slug' => 'burgerid',      'description' => 'Maitsevad burgerid',                  'sort_order' => 1, 'is_active' => true],
            ['name' => 'KEBAB',         'slug' => 'kebab',         'description' => 'Värskelt valmistatud kebabid',        'sort_order' => 2, 'is_active' => true],
            ['name' => 'PRAED',         'slug' => 'praed',         'description' => 'Küpsetatud praed',                   'sort_order' => 3, 'is_active' => true],
            ['name' => 'PITSAD',        'slug' => 'pitsad',        'description' => 'Itaalia stiilis pitsad',             'sort_order' => 4, 'is_active' => true],
            ['name' => 'SNÄKID',        'slug' => 'snakid',        'description' => 'Kiired suupisted',                   'sort_order' => 5, 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            $category = MenuCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
            $this->createItemsForCategory($category);
        }
    }

    private function createItemsForCategory(MenuCategory $category): void
    {
        $items = [];

        switch ($category->slug) {
            case 'eripakkumised':
                $items = [
                    ['name' => 'Suur rullkebab mahe või terav', 'description' => 'Suur rullkebab (mahe või terav)', 'price' => 9.44, 'original_price' => 11.80, 'is_featured' => true, 'discount_percentage' => 20, 'sort_order' => 0],
                ];
                break;
            case 'burgerid':
                $items = [
                    ['name' => 'Kebabipraad',                       'description' => 'Kebabiliha, friikartul, värske salat',                                                                                                    'price' => 9.80,  'is_featured' => true, 'sort_order' => 0],
                    ['name' => 'Kebabipraad mahe väike/suur',       'description' => 'Kebabiliha, friikartul, värske salat, kuuslaugukaste, jalapeno, salsakaste',                                                              'price' => 9.80,  'sort_order' => 1],
                    ['name' => 'Primo Special',                     'description' => 'Friikartul, 5 kananagitsit, 4 vinersi, kanaphy, praemuna, värske salat, Primo kaste, BBQ kaste',                                         'price' => 12.30, 'is_featured' => true, 'sort_order' => 2],
                    ['name' => 'Peekoniburger',                     'description' => 'Cheddar juust, grillitud veiselihapihv, pekon, tomat, marineeritud sibul, kurk, tillikaste, BBQ kaste',                                  'price' => 6.90,  'sort_order' => 3],
                    ['name' => 'Grillburger',                       'description' => 'Cheddar juust, grillitud veiselihapihv, jääsalat, tomat, marineeritud sibul, grillkaste',                                                'price' => 6.90,  'sort_order' => 4],
                ];
                break;
            case 'kebab':
                $items = [
                    ['name' => 'Krõbepraad', 'description' => 'Juust, kebabiliha, BBQ kaste', 'price' => 5.90, 'sort_order' => 0],
                ];
                break;
            case 'praed':
                $items = [
                    ['name' => 'Kebabipraad mahe või terav väike/suur', 'description' => 'Kebabiliha, friikartul, värske salat, BBQ kaste', 'price' => 9.80, 'sort_order' => 0],
                ];
                break;
            case 'pitsad':
                $items = [
                    ['name' => 'Margherita', 'description' => 'Tomatikaste, juust, oregano', 'price' => 7.50, 'sort_order' => 0],
                ];
                break;
            case 'snakid':
                $items = [
                    ['name' => 'Friikartul', 'description' => 'Kuldpruun friikartul', 'price' => 3.50, 'sort_order' => 0],
                ];
                break;
        }

        foreach ($items as $itemData) {
            $itemData['category_id'] = $category->id;
            $itemData['is_active'] = true;
            MenuItem::firstOrCreate(
                ['name' => $itemData['name'], 'category_id' => $category->id],
                $itemData
            );
        }
    }
}