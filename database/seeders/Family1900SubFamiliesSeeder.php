<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family1900SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1905')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1905',
                'name'     => 'Cogumelo',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1906')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1906',
                'name'     => 'Quiabo',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1907')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1907',
                'name'     => 'Gengibre',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1908')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1908',
                'name'     => 'Tremoço',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1909')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1909',
                'name'     => 'Trigo',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1910')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1910',
                'name'     => 'Ervilhaca',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1911')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1911',
                'name'     => 'Pimenta Moída',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1912')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1912',
                'name'     => 'Azeitona',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1913')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1913',
                'name'     => 'Polpa de Maracujá',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1914')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1914',
                'name'     => 'Flores Comestiveis',
                'category' => 20,
            ]);
        }


        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1915')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1915',
                'name'     => 'Ovos',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1916')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1916',
                'name'     => 'Corcuma',
                'category' => 20,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1917')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1917',
                'name'     => 'Peixe',
                'category' => 20,
            ]);
        }
    }
}
