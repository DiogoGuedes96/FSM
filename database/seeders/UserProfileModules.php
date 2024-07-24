<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProfileModules extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('user_profile_modules')->where('profile_id', 1)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'homepage',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'calls',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'order',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'products',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'orders-tracking',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'scheduling',
            ]);
        }

        if (!DB::table('user_profile_modules')->where('profile_id', 3)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'homepage',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'calls',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'order',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'orders-tracking',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'scheduling',
            ]);
        }

        $directSaleId = UserProfile::where('role', 'direct-sale')->first()->id;

        if (!DB::table('user_profile_modules')->where('profile_id', $directSaleId)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => $directSaleId,
                'module' => 'order',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => $directSaleId,
                'module' => 'orders-tracking',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => $directSaleId,
                'module' => 'direct-sale',
            ]);
        }

        if (!DB::table('user_profile_modules')->where('profile_id', 2)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 2,
                'module' => 'orders-tracking',
                'permissions' => json_encode([
                    'pending'     => true,
                    'preparation' => true,
                    'delivery'    => true,
                    'scheduling'  => false
                ]),
            ]);
        }

        $adminProfileExists = DB::table('user_profile_modules')
            ->where('profile_id', 1)
            ->exists();

        $attendentProfileExists = DB::table('user_profile_modules')
            ->where('profile_id', 3)
            ->exists();


        if ($adminProfileExists) {
            $moduleDirectSaleExists = DB::table('user_profile_modules')
                ->where('profile_id', 1)
                ->where('module', 'direct-sale')
                ->exists();

            $moduleCallsExist = DB::table('user_profile_modules')
                ->where('profile_id', 1)
                ->where('module', 'calls')
                ->exists();

            $moduleScheduleExist = DB::table('user_profile_modules')
                ->where('profile_id', 1)
                ->where('module', 'scheduling')
                ->exists();

            $moduleOrdersTrackingExists = DB::table('user_profile_modules')
                ->where('profile_id', 1)
                ->where('module', 'orders-tracking')
                ->exists();

            if ($moduleCallsExist) {
                DB::table('user_profile_modules')
                    ->where('profile_id', 1)
                    ->where('module', 'calls')
                    ->update([
                        'permissions' => json_encode([
                            'terminateCalls' => true,
                        ]),
                    ]);
            }

            if(!$moduleDirectSaleExists){
                DB::table('user_profile_modules')->insert([
                    'profile_id'  => 1,
                    'module' => 'direct-sale',
                ]);
            }

            if ($moduleOrdersTrackingExists) {
                DB::table('user_profile_modules')
                ->where('profile_id', 1)
                ->where('module', 'orders-tracking')
                ->update([
                    'permissions' => json_encode([
                        'search'            => true,
                        'unblockOrder'      => true,
                        'draft'           => true,
                        'pending'           => true,
                        'partially-shipped' => true,
                        'preparation'       => true,
                        'delivery'          => true,
                        'completed'         => true,
                        'canceled'          => true,
                    ]),
                ]);
            }

            if ($moduleScheduleExist) {
                DB::table('user_profile_modules')
                    ->where('profile_id', 1)
                    ->where('module', 'scheduling')
                    ->update([
                        'permissions' => json_encode([
                            'accessRemindersBtn' => true,
                        ]),
                    ]);
            }
        }

        if ($attendentProfileExists) {
            $moduleScheduleExist = DB::table('user_profile_modules')
                ->where('profile_id',3)
                ->where('module', 'scheduling')
                ->exists();

            $moduleOrdersTrackingExists = DB::table('user_profile_modules')
                ->where('profile_id',3)
                ->where('module', 'orders-tracking')
                ->exists();


            if ($moduleScheduleExist) {
                DB::table('user_profile_modules')
                    ->where('profile_id', 3)
                    ->where('module', 'scheduling')
                    ->update([
                        'permissions' => json_encode([
                            'accessRemindersBtn' => true,
                        ]),
                    ]);
            }

            if ($moduleOrdersTrackingExists) {
                DB::table('user_profile_modules')
                    ->where('profile_id', 3)
                    ->where('module', 'orders-tracking')
                    ->update([
                        'permissions' => json_encode([
                            'search'            => true,
                            'unblockOrder'      => true,
                            'draft'           => true,
                            'pending'           => true,
                            'partially-shipped' => true,
                            'preparation'       => true,
                            'delivery'          => true,
                            'completed'         => true,
                            'canceled'          => true,
                        ]),
                    ]);
            }
        }
    }
}
