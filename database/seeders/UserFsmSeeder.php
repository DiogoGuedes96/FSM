<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFsmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = DB::table('user_profile')->where('role', 'admin')->first('id');
        $shipper = DB::table('user_profile')->where('role', 'shipper')->first('id');
        $attendant = DB::table('user_profile')->where('role', 'attendant')->first('id');
        $directSale = DB::table('user_profile')->where('role', 'directSale')->first('id');

        $fsmUsers = [
            ['Manuel Meideiros',    '7@fsm',    $directSale],
            ['Emanuel Aguiar',    '9@fsm',    $directSale],
            ['António Amaral',    '10@fsm',    $admin],
            ['Paulo Cordeiro',    '13@fsm',    $directSale],
            ['Tais Braga',    '14@fsm',    $directSale],
            ['Helder Raposo',    '24@fsm',    $admin],
            ['Catarina Monte',    '27@fsm',    $directSale],
            ['António Resendes',    '30@fsm',    $directSale],
            ['Ruben Amaral',    '31@fsm',    $directSale],
            ['Gerson Silva',    '36@fsm',    $directSale],
            ['Césaro Moniz',    '37@fsm',    $directSale],
            ['Fernando Carreiro',    '39@fsm',    $directSale],
            ['João Melo',    '50@fsm',    $directSale],
            ['Marco Coelho',    '55@fsm',    $directSale],
            ['Karina Luis',    '56@fsm',    $admin],
            ['Hugo Medeiros',    '58@fsm',    $directSale],
            ['Roberto Joaquim',    '59@fsm',    $attendant],
            ['David Raposo',    '62@fsm',    $directSale],
            ['Débora Ponte',    '63@fsm',    $attendant],
            ['Roberto Camera',    '64@fsm',    $directSale],
            ['Arménio Dinis',    '66@fsm',    $admin],
            ['Milton Lima',    '67@fsm',    $directSale],
            ['Bruno Gapar',    '68@fsm',    $directSale],
            ['Bruno Correia',    '69@fsm',    $directSale],
            ['Carlos Santos',    '70@fsm',    $directSale],
            ['Sandro Martins',    '71@fsm',    $directSale],
            ['Nuno Melo',    '72@fsm',    $directSale],
            ['Marcos Lopes',    '74@fsm',    $attendant],
            ['Tomas Cordeiro',    '75@fsm',    $attendant],
        ];

        foreach ($fsmUsers as $fsmUser) {
            if (!DB::table('users')->where('email', $fsmUser[1])->exists()) {
                DB::table('users')->insert([
                    'name'       => $fsmUser[0],
                    'email'      => $fsmUser[1],
                    'password'   => bcrypt($fsmUser[1]),
                    'phone'      => '987654321',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'profile_id' => $fsmUser[2]->id
                ]);
            }
        }
    }
}
