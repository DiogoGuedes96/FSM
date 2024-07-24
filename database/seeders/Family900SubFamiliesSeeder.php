<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family900SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '901')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '901',
                'name'     => 'Castanha',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '902')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '902',
                'name'     => 'Diospiro',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '903')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '903',
                'name'     => 'Figo',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '904')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '904',
                'name'     => 'Marmelo',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '906')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '906',
                'name'     => 'Anona',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '907')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '907',
                'name'     => 'Romã',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '908')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '908',
                'name'     => 'Capucho',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '910')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '910',
                'name'     => 'Kunquat',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '913')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '913',
                'name'     => 'Tamarilho',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '914')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '914',
                'name'     => 'Litchies',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '916')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '916',
                'name'     => 'Pitahayas',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '917')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '917',
                'name'     => 'Maracujá',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '918')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '918',
                'name'     => 'Goiaba',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '919')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '919',
                'name'     => 'Physalis',
                'category' => 10,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '920')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '920',
                'name'     => 'Tamarilho',
                'category' => 10,
            ]);
        }
    }
}
