<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhoneBlacklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('phone_blacklist')->where('phone', '296302110')->exists()) {
            DB::table('phone_blacklist')->insert([
                'phone'       => '296302110',
            ]);
        }
    }
}
