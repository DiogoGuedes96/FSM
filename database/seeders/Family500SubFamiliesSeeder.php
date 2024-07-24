<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family500SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '501')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '501',
                'name'     => 'Uva Branca',
                'category' => 6,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '502')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '502',
                'name'     => 'Uva Preta',
                'category' => 6,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '503')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '503',
                'name'     => 'Uva Red Globe',
                'category' => 6,
            ]);
        }
    }
}
