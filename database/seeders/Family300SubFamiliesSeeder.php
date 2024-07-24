<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family300SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '301')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '301',
                'name'     => 'Pêra Lawson',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '302')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '302',
                'name'     => 'Pêra Moretini',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '303')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '303',
                'name'     => 'Pêra Ercolini',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '304')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '304',
                'name'     => 'Pêra Pérola',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '305')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '305',
                'name'     => 'Pêra Packams',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '306')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '306',
                'name'     => 'Pêra Vermelha',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '307')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '307',
                'name'     => 'Pêra Anjou',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '308')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '308',
                'name'     => 'Pêra Rocha',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '309')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '309',
                'name'     => 'Pêra Vitória',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '310')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '310',
                'name'     => 'Pêra Williams',
                'category' => 4,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '311')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '311',
                'name'     => 'Pêra Passe Crassane',
                'category' => 4,
            ]);
        }
    }
}
