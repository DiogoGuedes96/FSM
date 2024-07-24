<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;

class UserController extends Controller 
{
    private $user;
    private $userProfile;

    public function __construct()
    {

        $this->user = new User();
        $this->userProfile = new UserProfile();
    }

    public function changeShipperRole() {
        try {
            $directSaleId = $this->userProfile->where('role', 'direct-sale')->first()->id;
            $shipperId = $this->userProfile->where('role', 'shipper')->first()->id;

            $this->user->where('profile_id', $shipperId)->update(['profile_id' =>  $directSaleId]);

            return response()->json([
                'message' => 'Success',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error'   => $th
            ]);        }
    }
}
