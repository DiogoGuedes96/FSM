<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family1700SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1701')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1701',
                'name'     => 'Banana Regional',
                'category' => 18,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1702')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1702',
                'name'     => 'Banana da Madeira',
                'category' => 18,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1703')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1703',
                'name'     => 'Banana Importada',
                'category' => 18,
            ]);
        }
    }
}
