<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vendor::create([
            'name' => 'Vendor1',
            'code' => '000001',
            'type' => 'distribution',
            'paymenttype' => 'cash',
            'is_active' => 1

        ]);
        Vendor::create([
            'name' => 'Vendor2',
            'code' => '000002',
            'type' => 'manufacturing',
            'paymenttype' => 'credit',
            'is_active' => 1

        ]);
        Vendor::create([
            'name' => 'Vendor3',
            'code' => '000003',
            'type' => 'transportcompany',
            'paymenttype' => 'cash',
            'is_active' => 1

        ]);
    }
}
