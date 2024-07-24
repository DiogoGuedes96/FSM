<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family400SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '401')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '401',
                'name'     => 'Nectarina',
                'category' => 5,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '402')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '402',
                'name'     => 'Pêssego',
                'category' => 5,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '403')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '403',
                'name'     => 'Alperce',
                'category' => 5,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '404')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '404',
                'name'     => 'Ameixa',
                'category' => 5,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '405')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '405',
                'name'     => 'Nespera',
                'category' => 5,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '406')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '406',
                'name'     => 'Cereja',
                'category' => 5,
            ]);
        }
    }
}
