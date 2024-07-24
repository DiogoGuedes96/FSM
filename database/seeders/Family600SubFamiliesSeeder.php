<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family600SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '601')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '601',
                'name'     => 'Morango',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '602')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '602',
                'name'     => 'Tâmaras',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '603')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '603',
                'name'     => 'Carambola',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '604')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '604',
                'name'     => 'Framboesa',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '605')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '605',
                'name'     => 'Mirtilo',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '606')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '606',
                'name'     => 'Groselha',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '607')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '607',
                'name'     => 'Bagas/Berries',
                'category' => 7,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '608')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '608',
                'name'     => 'Mini Frutos',
                'category' => 7,
            ]);
        }
    }
}
