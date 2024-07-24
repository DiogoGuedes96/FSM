<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BmsProductsCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return voerp_category_code
     */
    public function run()
    {
        if (
            DB::table('bms_products_categories')->exists() && 
            !DB::table('bms_products_categories')
                ->where('erp_category_code', '0000')
                ->where('id', 1)->exists()) {
            DB::table('bms_products_categories')->delete();
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0000')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 1,
                'erp_category_code' => '0000',
                'name'              => 'Diversos',
                'group'             => 'others',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0100')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 2,
                'erp_category_code' => '0100',
                'name'              => 'Citrinos',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0200')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 3,
                'erp_category_code' => '0200',
                'name'              => 'Maçãs',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0300')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 4,
                'erp_category_code' => '0300',
                'name'              => 'Pêras',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0400')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 5,
                'erp_category_code' => '0400',
                'name'              => 'Frutas De Caroço',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0500')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 6,
                'erp_category_code' => '0500',
                'name'              => 'Uvas',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0600')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 7,
                'erp_category_code' => '0600',
                'name'              => 'Pequenos Frutos',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0700')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 8,
                'erp_category_code' => '0700',
                'name'              => 'Frutos Tropicais',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0800')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 9,
                'erp_category_code' => '0800',
                'name'              => 'Frutos da Terra',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '0900')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 10,
                'erp_category_code' => '0900',
                'name'              => 'Sazonais',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1000')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 11,
                'erp_category_code' => '1000',
                'name'              => 'Aromáticos',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1100')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 12,
                'erp_category_code' => '1100',
                'name'              => 'Cebolas',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1200')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 13,
                'erp_category_code' => '1200',
                'name'              => 'Couves/Repolhos',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1300')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 14,
                'erp_category_code' => '1300',
                'name'              => 'Legumes Para Salada',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1400')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 15,
                'erp_category_code' => '1400',
                'name'              => 'Legumes Para Sopa',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1500')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 16,
                'erp_category_code' => '1500',
                'name'              => 'Tubérculos',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1600')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 17,
                'erp_category_code' => '1600',
                'name'              => 'Tomates',
                'group'             => 'vegetables',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1700')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 18,
                'erp_category_code' => '1700',
                'name'              => 'Bananas',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1800')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 19,
                'erp_category_code' => '1800',
                'name'              => 'Frutos Secos',
                'group'             => 'fruits',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1900')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 20,
                'erp_category_code' => '1900',
                'name'              => 'Outros',
                'group'             => 'others',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1910')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 21,
                'erp_category_code' => '1910',
                'name'              => 'Chocolates',
                'group'             => 'others',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '1990')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 22,
                'erp_category_code' => '1990',
                'name'              => 'Bebidas',
                'group'             => 'others',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '2000')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 23,
                'erp_category_code' => '2000',
                'name'              => 'Sacos/Embalagens',
                'group'             => 'others',
            ]);
        }

        if (!DB::table('bms_products_categories')->where('erp_category_code', '9999')->exists()) {
            DB::table('bms_products_categories')->insert([
                'id'                => 24,
                'erp_category_code' => '9999',
                'name'              => 'Vasilhame Retornável',
                'group'             => 'others',
            ]);
        }
    }
}
