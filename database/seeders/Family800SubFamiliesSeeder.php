<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family800SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '801')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '801',
                'name'     => 'Melancia',
                'category' => 9,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '802')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '802',
                'name'     => 'Melão',
                'category' => 9,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '803')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '803',
                'name'     => 'Meloa',
                'category' => 9,
            ]);
        }
    }
}
