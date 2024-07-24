<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family1910SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1910')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1910',
                'name'     => 'Amendoas Chocolate',
                'category' => 21,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1920')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1920',
                'name'     => 'Bombom Vitors',
                'category' => 21,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1930')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1930',
                'name'     => 'Bombom Sorini',
                'category' => 21,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1950')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1950',
                'name'     => 'Bombom Coração',
                'category' => 21,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1990')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1990',
                'name'     => 'Outros Chocolate',
                'category' => 21,
            ]);
        }
    }
}
