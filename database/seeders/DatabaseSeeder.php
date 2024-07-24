<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserProfileSeed::class,
            UserSeeder::class,
            PhoneBlacklistSeeder::class,
            BmsProductsCategoriesSeeder::class,
            UserProfileModules::class,
            Family0001SubFamiliesSeeder::class,
            Family100SubFamiliesSeeder::class,
            Family200SubFamiliesSeeder::class,
            Family300SubFamiliesSeeder::class,
            Family400SubFamiliesSeeder::class,
            Family500SubFamiliesSeeder::class,
            Family600SubFamiliesSeeder::class,
            Family700SubFamiliesSeeder::class,
            Family800SubFamiliesSeeder::class,
            Family900SubFamiliesSeeder::class,
            Family1000SubFamiliesSeeder::class,
            Family1100SubFamiliesSeeder::class,
            Family1200SubFamiliesSeeder::class,
            Family1300SubFamiliesSeeder::class,
            Family1400SubFamiliesSeeder::class,
            Family1500SubFamiliesSeeder::class,
            Family1600SubFamiliesSeeder::class,
            Family1700SubFamiliesSeeder::class,
            Family1800SubFamiliesSeeder::class,
            Family1900SubFamiliesSeeder::class,
            Family1910SubFamiliesSeeder::class,
            Family1990SubFamiliesSeeder::class,
            Family2000SubFamiliesSeeder::class,
            UserFsmSeeder::class,
        ]);
    }
}
