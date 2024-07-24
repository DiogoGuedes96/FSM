<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('users')->where('name', 'Administrador')->exists()) {
            DB::table('users')->insert([
                'name'       => 'Administrador',
                'email'      => 'bmsadmin@integer.pt',
                'password'   => '$2y$10$m5AyVB/UIm//9w5vBT1rgOYRlIM2JAcHBfNdbo/ZYWSPA/Y5qNOs6',
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile_id' => '1'
            ]);
        }
        if (!DB::table('users')->where('name', 'Expeditor')->exists()) {
            DB::table('users')->insert([
                'name'       => 'Expeditor',
                'email'      => 'bmsshipper@integer.pt',
                'password'   => '$2y$10$B8PH4cXYB6sgf8NNB2rMfuV1q9.DX71lPLRYl4WeGNvWvBQRKDZTe',//bcrypt('integerbms@ship...'),
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile_id' => '2'
            ]);
        }
        if (!DB::table('users')->where('name', 'Atendente')->exists()) {
            DB::table('users')->insert([
                'name'       => 'Atendente',
                'email'      => 'bmsattendant@integer.pt',
                'password'   => '$2y$10$Zxv2SgH0It0e45M311cu/uyTAQ.h3DDbk1Gd4nbigS3ynEa83QHA2',//bcrypt('integerbms@atte...'),
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile_id' => '3'
            ]);
        }

        if (DB::table('users')->where('name', 'admin')->exists()) {
            DB::table('users')->where('name', 'admin')->update(['name' => 'Administrador']);
        }

        if (DB::table('users')->where('name', 'shipper')->exists()) {
            DB::table('users')->where('name', 'shipper')->update(['name' => 'Expeditor']);
        }

        if (DB::table('users')->where('name', 'attendant')->exists()) {
            DB::table('users')->where('name', 'attendant')->update(['name' => 'Atendente']);
        }
    }
}
