<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Maaş', 'is_default' => true],
            ['name' => 'Freelance', 'is_default' => true],
            ['name' => 'Market', 'is_default' => true],
            ['name' => 'Fatura', 'is_default' => true],
            ['name' => 'Eğlence', 'is_default' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
