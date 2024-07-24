<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family100SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '101')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => '101',
                'name'                  => 'Clementina',
                'category'              => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '102')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '102',
                'name'     => 'Laranja',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '103')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '103',
                'name'     => 'Limão',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '104')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '104',
                'name'     => 'Mandarina',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '105')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '105',
                'name'     => 'Tangerina',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '106')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '106',
                'name'     => 'Toranja',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '107')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '107',
                'name'     => 'Tangera',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '108')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '108',
                'name'     => 'Clemenvilha',
                'category' => 2,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '109')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '109',
                'name'     => 'Lima',
                'category' => 2,
            ]);
        }
    }
}
