<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Family0001SubFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'CTBL01')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'CTBL01',
                'name'                  => 'Custos',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'FRT')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'FRT',
                'name'                  => 'Fretes Mercadoria',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'IMO')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'IMO',
                'name'                  => 'Imobilizado',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'RPLCT')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'RPLCT',
                'name'                  => 'Rappel Continente',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'RPLGM')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'RPLGM',
                'name'                  => 'Rappel Grupo Marques',
                'category'              => 1,
            ]);
        }


        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'RPLMS')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'RPLMS',
                'name'                  => 'Rappel Meu Super',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'RPLOT')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'RPLOT',
                'name'                  => 'Rappel Outros',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'Laticinios')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'Laticinios',
                'name'                  => 'Laticinios',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'Sumos')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'Sumos',
                'name'                  => 'Sumos',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'Mercearia')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'Mercearia',
                'name'                  => 'Mercearia',
                'category'              => 1,
            ]);
        }

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'AZ00')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'AZ00',
                'name'                  => 'Loja Azevedos',
                'category'              => 1,
            ]);
        }
        

        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'AZ01')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'AZ01',
                'name'                  => 'Frutaria',
                'category'              => 1,
            ]);
        }


        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'AZ02')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'AZ02',
                'name'                  => 'WOP',
                'category'              => 1,
            ]);
        }


        if (!DB::table('bms_products_sub_categories')->where('erp_sub_category_code', 'AZ03')->exists()) {
            DB::table('bms_products_sub_categories')->insert([
                'erp_sub_category_code' => 'AZ03',
                'name'                  => 'Lotes Valados',
                'category'              => 1,
            ]);
        }
    }
}
