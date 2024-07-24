<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family1000SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1001')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1001',
                'name'     => 'Coentros',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1002')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1002',
                'name'     => 'Funcho',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1003')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1003',
                'name'     => 'Hortelã',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1004')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1004',
                'name'     => 'Malagueta',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1005')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1005',
                'name'     => 'Salsa',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1006')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1006',
                'name'     => 'Alho Seco',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1007')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1007',
                'name'     => 'Mostarda',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1008')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1008',
                'name'     => 'Alecrim',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1009')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1009',
                'name'     => 'Tomilho',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1010')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1010',
                'name'     => 'Mangericão',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1011')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1011',
                'name'     => 'Erva Limão',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1012')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1012',
                'name'     => 'Salva',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1013')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1013',
                'name'     => 'Piri-Piri',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1014')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1014',
                'name'     => 'Cebolinho',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1015')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1015',
                'name'     => 'Agrião',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1016')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1016',
                'name'     => 'Rucula',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1017')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1017',
                'name'     => 'Estragão',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1018')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1018',
                'name'     => 'Rosmaninho',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1019')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1019',
                'name'     => 'Pejo',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1020')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1020',
                'name'     => 'Oregão',
                'category' => 11,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1999')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1999',
                'name'     => 'Outros (Vários)',
                'category' => 11,
            ]);
        }
    }
}
