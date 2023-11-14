<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 700ML',
            'code' => '2601000011',
            'uom' => 'BOT',
            'sku' => 'GRAND ROYAL WHISKY BLACK 700ML-BOT',
            'qty' => '24',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 700ML',
            'code' => '2601000012',
            'uom' => 'BOX',
            'sku' => 'GRAND ROYAL WHISKY BLACK 700ML-BOX',
            'qty' => '5',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 350ML',
            'code' => '2601000092',
            'uom' => 'BOT',
            'sku' => 'GRAND ROYAL WHISKY BLACK 350ML-BOT',
            'qty' => '48',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 350ML',
            'code' => '2601000090',
            'uom' => 'BOX',
            'sku' => 'GRAND ROYAL WHISKY BLACK 350ML-BOX',
            'qty' => '2',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 175ML',
            'code' => '2601000034',
            'uom' => 'BOT',
            'sku' => 'GRAND ROYAL WHISKY BLACK 175ML-BOT',
            'qty' => '48',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 175ML',
            'code' => '2601000035',
            'uom' => 'BOX',
            'sku' => 'GRAND ROYAL WHISKY BLACK 175ML-BOX',
            'qty' => '2',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 90ML',
            'code' => '2601000040',
            'uom' => 'BOT',
            'sku' => 'GRAND ROYAL WHISKY BLACK 175ML-BOT',
            'qty' => '96',
            
        ]);
        Product::create([
            'name' => 'GRAND ROYAL WHISKY BLACK 90ML',
            'code' => '2601000041',
            'uom' => 'BOX',
            'sku' => 'GRAND ROYAL WHISKY BLACK 175ML-BOX',
            'qty' => '2',
            
        ]);
        Product::create([
            'name' => 'CALSOME NUTRITIOUS CEREAL DRINK 30SX25G',
            'code' => '1202000019',
            'uom' => 'PKT',
            'sku' => 'CALSOME NUTRITIOUS CEREAL DRINK 30SX25G-PKT',
            'qty' => '30',
            
        ]);
        Product::create([
            'name' => 'CALSOME NUTRITIOUS CEREAL DRINK 30SX750G',
            'code' => '1202000020',
            'uom' => 'BOX',
            'sku' => 'CALSOME NUTRITIOUS CEREAL DRINK 30SX25G-BOX',
            'qty' => '3',
            
        ]);
        Product::create([
            'name' => 'CALSOME NUTRITIOUS CEREAL DRINK 25G',
            'code' => '1202000021',
            'uom' => 'PCS',
            'sku' => 'CALSOME NUTRITIOUS CEREAL DRINK 25G-PCS',
            'qty' => '120',
            
        ]);
        Product::create([
            'name' => 'SUNDAY 3IN1 COFFEE MIX 30SX30G',
            'code' => '1103000001',
            'uom' => 'PKT',
            'sku' => 'SUNDAY 3IN1 COFFEE MIX 30SX30G-PKT',
            'qty' => '40',
            
        ]);
        Product::create([
            'name' => 'SUNDAY 3IN1 COFFEE MIX 30SX750G',
            'code' => '1103000002',
            'uom' => 'BOX',
            'sku' => 'SUNDAY 3IN1 COFFEE MIX 30SX750G-BOX',
            'qty' => '3',
            
        ]);
        Product::create([
            'name' => 'SUNDAY 3IN1 COFFEE MIX 30G',
            'code' => '1103000003',
            'uom' => 'PCS',
            'sku' => 'SUNDAY 3IN1 COFFEE MIX 30G-PCS',
            'qty' => '150',
            
        ]);
         Product::create([
            'name' => 'SUNDAY 3IN1 TEA MIX 30SX30G',
            'code' => '1103000011',
            'uom' => 'PKT',
            'sku' => 'SUNDAY 3IN1 TEA MIX 30SX30G-PKT',
            'qty' => '50',
            
        ]);
        Product::create([
            'name' => 'SUNDAY 3IN1 TEA MIX 30SX750G',
            'code' => '1103000012',
            'uom' => 'BOX',
            'sku' => 'SUNDAY 3IN1 TEA MIX 30SX750G-BOX',
            'qty' => '2',
            
        ]);
        Product::create([
            'name' => 'SUNDAY 3IN1 TEA MIX 30G',
            'code' => '1103000013',
            'uom' => 'PCS',
            'sku' => 'SUNDAY 3IN1 TEA MIX 30G-PCS',
            'qty' => '90',
            
        ]);
    }
}
