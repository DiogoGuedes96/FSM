<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family200SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '201')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '201',
                'name'     => 'Maçã Beleza da Roma',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '202')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '202',
                'name'     => 'Maçã Early Gold',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '203')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '203',
                'name'     => 'Maçã Gloster',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '204')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '204',
                'name'     => 'Maçã Golden',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '205')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '205',
                'name'     => 'Maçã Jersey Mac',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '206')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '206',
                'name'     => 'Maçã Jonathan',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '207')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '207',
                'name'     => 'Maçã Mitsu',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '208')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '208',
                'name'     => 'Maçã Ozarkgold',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '209')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '209',
                'name'     => 'Maçã Reineta Parda',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '210')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '210',
                'name'     => 'Maçã Reineta Verde',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '211')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '211',
                'name'     => 'Maçã Royal Gala',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '212')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '212',
                'name'     => 'Maçã Smith',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '213')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '213',
                'name'     => 'Maçã Starking',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '214')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '214',
                'name'     => 'Maçã Idared',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '215')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '215',
                'name'     => 'Maçã Morgenduft',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '216')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '216',
                'name'     => 'Maçã Jonagold',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '217')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '217',
                'name'     => 'Maçã Jonagored',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '218')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '218',
                'name'     => 'Maçã Fuji',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '219')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '219',
                'name'     => 'Maçã Espelho',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '220')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '220',
                'name'     => 'Maçã Pink Lady',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '221')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '221',
                'name'     => 'Maçã Pink Crisp',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '222')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '222',
                'name'     => 'Maçã Bravo Esmolfe',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '223')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '223',
                'name'     => 'Maçã Riscadinha',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '224')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '224',
                'name'     => 'Maçã Golden Suprema',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '225')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '225',
                'name'     => 'Maçã Early Golden',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '226')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '226',
                'name'     => 'Maçã Gingergold',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '227')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '227',
                'name'     => 'Maçã Ginger Golden',
                'category' => 3,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', '228')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code'       => '228',
                'name'     => 'Maçã Reineta',
                'category' => 3,
            ]);
        }
    }
}
