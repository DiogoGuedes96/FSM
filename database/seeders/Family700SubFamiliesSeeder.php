<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family700SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '701')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '701',
                'name'     => 'Abacaxi',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '702')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '702',
                'name'     => 'Ananás',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '703')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '703',
                'name'     => 'Kiwi',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '704')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '704',
                'name'     => 'Mamão',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '705')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '705',
                'name'     => 'Manga',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '706')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '706',
                'name'     => 'Papaia',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '707')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '707',
                'name'     => 'Côcô',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '708')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '708',
                'name'     => 'Mandioca			',
                'category' => 8,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '709')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '709',
                'name'     => 'Abacate',
                'category' => 8,
            ]);
        }
    }
}
