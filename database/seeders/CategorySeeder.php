<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Lensa', 'Body Only', 'Flash', 'Tripod', 'Aksesoris'];

        foreach ($categories as $nama) {
            Category::create(['nama' => $nama]);
        }
    }
}
