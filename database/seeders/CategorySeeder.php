<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Design', 'slug' => 'design'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
            ['name' => 'News', 'slug' => 'news'],
            ['name' => 'Guide', 'slug' => 'guide'],
            ['name' => 'Review', 'slug' => 'review'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}