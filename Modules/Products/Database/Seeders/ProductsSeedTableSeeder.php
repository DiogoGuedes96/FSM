<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductsSeedTableSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Product 1',
                'avg_price' => 10.0,
                'last_price' => 12.0,
                'sell_unit' => 'unit',
                'current_stock' => 100,
                'stock_mov' => 'in',
                'family' => 'Family 1',
                'sub_family' => 'Sub-Family 1',
                'pvp_1' => 15.0,
                'pvp_2' => 20.0,
                'pvp_3' => 25.0,
                'pvp_4' => 30.0,
                'pvp_5' => 35.0,
                'pvp_6' => 40.0,
                'erp_product_id' => 'ABC001',
                'primavera_table_id' => 1,
            ],
            [
                'name' => 'Product 2',
                'avg_price' => 20.0,
                'last_price' => 22.0,
                'sell_unit' => 'kg',
                'current_stock' => 50,
                'stock_mov' => 'out',
                'family' => 'Family 2',
                'sub_family' => 'Sub-Family 2',
                'pvp_1' => 30.0,
                'pvp_2' => 40.0,
                'pvp_3' => 50.0,
                'pvp_4' => 60.0,
                'pvp_5' => 70.0,
                'pvp_6' => 80.0,
                'erp_product_id' => 'ABC002',
                'primavera_table_id' => 2,
            ],
            [
                'name' => 'Product 2',
                'avg_price' => 20.0,
                'last_price' => 22.0,
                'sell_unit' => 'kg',
                'current_stock' => 50,
                'stock_mov' => 'out',
                'family' => 'Family 2',
                'sub_family' => 'Sub-Family 2',
                'pvp_1' => 30.0,
                'pvp_2' => 40.0,
                'pvp_3' => 50.0,
                'pvp_4' => 60.0,
                'pvp_5' => 70.0,
                'pvp_6' => 80.0,
                'erp_product_id' => 'ABC002',
                'primavera_table_id' => 2,
            ],
            // add more products as needed
        ];

        // Loop through each product and insert it into the database
        foreach ($products as $product) {
            $item = DB::table('bms_products')->insertGetId($product);
            $product['avg_price'] = $product['avg_price'] + 1;
            DB::table('bms_products')->where('id', $item)->update($product);
        }
    }
}
