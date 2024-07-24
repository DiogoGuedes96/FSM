<?php

namespace Modules\Calls\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Calls\Entities\AsteriskCall;
use Modules\Calls\Services\AsteriskService;
use Modules\Clients\Entities\Clients;
use Modules\Orders\Services\OrdersService;
use Modules\Calls\Services\CallsService;

class CallsController extends Controller
{

    private $asteriskService;
    private $callsService;
    private $ordersServices;

    public function __construct()
    {
        $this->asteriskService = new AsteriskService();
        $this->ordersServices  = new OrdersService();
        $this->callsService     = new CallsService();
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('calls::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('calls::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('calls::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('calls::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inProgress(Request $request)
    {
        try{
            $calls = $this->asteriskService->getCallsInProgress();

            return response()->json([
                'message' => 'Calls in progress',
                'calls' => $calls
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hangup(Request $request)
    {
        try{
            $calls = $this->asteriskService->getCallsHangup();

            return response()->json(
            array_merge(['message' => 'Calls hangup'], $calls)
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missed(Request $request)
    {
        try {
            $calls = $this->asteriskService->getCallsMissed();

            return response()->json(
                array_merge(['message' => 'Calls missed'], $calls)
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOrdersDetailsByClientId(Clients $client, Request $request) {
        try{
            $startDate = $request->query('startDate', null);
            $endDate = $request->query('endDate', null);

            $products = $this->ordersServices->getProductsOrdersByClientId($client, $startDate, $endDate); //The request is only used to filter the orderProducts not the orders themselves
            return response()->json([
                'products' => $products,
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function closeGhostCall($callId){
        try{
            $this->callsService->terminateCall($callId);
            return response()->json(['Call with ID: '.$callId.' has been terminated successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function countMissed(){
        try{
            $calls = $this->asteriskService->getCallsMissed(true);
            return response()->json([
                'message' => 'Calls missed',
                'calls' => $calls
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setViewed(Request $request){
        try{
            if (isset($request->calls)) {
                $this->asteriskService->setViewed($request->calls);
            }
            
            return response()->json([
                'message' => 'Success',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
