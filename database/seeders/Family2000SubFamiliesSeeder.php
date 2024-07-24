<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family2000SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '2001')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '2001',
                'name'     => 'Sacos',
                'category' => 23,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '2002')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '2002',
                'name'     => 'Embalagens',
                'category' => 23,
            ]);
        }
    }
}
