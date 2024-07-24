<?php

namespace Modules\Calls\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Calls\Services\AsteriskService;

class AsteriskController extends Controller
{

    /**
     * @var AsteriskService
     */
    private $asteriskService;

    /**
     * @var AsteriskService
     */
    private $asteriskCredentials;

    public function __construct()
    {
        $this->asteriskService = new AsteriskService();
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if(!$request->internal_pw){
            return response()->json(['message' => 'A password is needed to change the Credentials', 'error' => "Please insert the password"], 404);
        }
        
        return $this->asteriskService->asteriskCredentialsIndex($request);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function update(Request $request)
    {
        if(!$request->internal_pw){
            return response()->json(['message' => 'A password is needed to change the Credentials', 'error' => "Please insert the password"], 404);
        }

        return $this->asteriskService->asteriskCredentialsUpdate($request);

    }
}
