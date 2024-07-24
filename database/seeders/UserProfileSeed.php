<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProfileSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('user_profile')->where('role', 'admin')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'admin',
                'description' => 'System Admin',
            ]);
        }
        if (!DB::table('user_profile')->where('role', 'shipper')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'shipper',
                'description' => 'System shipper',
            ]);
        }
        if (!DB::table('user_profile')->where('role', 'attendant')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'attendant',
                'description' => 'System attendant',
            ]);
        }
        if (!DB::table('user_profile')->where('role', 'direct-sale')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'direct-sale',
                'description' => 'System direct sale attendant',
            ]);
        }
    }
}
