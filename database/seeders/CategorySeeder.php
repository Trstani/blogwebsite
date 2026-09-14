<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Design',     'slug' => 'design',     'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lifestyle',  'slug' => 'lifestyle',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'News',       'slug' => 'news',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Guide',      'slug' => 'guide',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Review',     'slug' => 'review',     'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
