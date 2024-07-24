<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family1400SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1401')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1401',
                'name'     => 'Abóbora',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1402')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1402',
                'name'     => 'Alho Francês',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1403')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1403',
                'name'     => 'Cenoura',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1404')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1404',
                'name'     => 'Ervilha',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1405')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1405',
                'name'     => 'Fava',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1406')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1406',
                'name'     => 'Couve Nabiça',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1407')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1407',
                'name'     => 'Espinafres',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1408')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1408',
                'name'     => 'Feijão',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1409')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1409',
                'name'     => 'Grelo',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1410')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1410',
                'name'     => 'Nabo sem Rama',
                'category' => 15,
            ]);
        }


        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1411')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1411',
                'name'     => 'Maçaroca',
                'category' => 15,
            ]);
        }


        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1412')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1412',
                'name'     => 'Courgette',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1413')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1413',
                'name'     => 'Grão de Bico',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1414')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1414',
                'name'     => 'Repolho',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1415')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1415',
                'name'     => 'Caldo Verde',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1416')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1416',
                'name'     => 'Mini Legumes',
                'category' => 15,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '1417')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '1417',
                'name'     => 'Courgette',
                'category' => 15,
            ]);
        }
    }
}
