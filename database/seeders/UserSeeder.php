<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'firstname' => 'Rathan',
            'lastname' => 'Poudel',
            'email' => 'rathan@admin.com',
            'username' => 'rathan406',
            'password' => Hash::make('rathan406'),
            'phone' => '0987654321',
            'address' => 'Yangon',
        ]);
        User::create([
            'firstname' => 'Sara',
            'lastname' => 'Sutar',
            'email' => 'gr@shop.com',
            'username' => 'adminsara',
            'password' => Hash::make('adminsara'),
            'phone' => '0987654321',
            'address' => 'Bangkok',
        ]);
    }
}
