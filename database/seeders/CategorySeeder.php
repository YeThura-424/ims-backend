<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Category1',
            'code' => '000001'
        ]);
        Category::create([
            'name' => 'Category2',
            'code' => '000002'
        ]);
        Category::create([
            'name' => 'Category3',
            'code' => '000003'
        ]);
    }
}
