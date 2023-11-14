<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'name' => 'Brand1',
            'code' => '000001'
        ]);
        Brand::create([
            'name' => 'Brand2',
            'code' => '000002'
        ]);
        Brand::create([
            'name' => 'Brand3',
            'code' => '000003'
        ]);
    }
}
