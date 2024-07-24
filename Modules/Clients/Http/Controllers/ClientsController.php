<?php

namespace Modules\Clients\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Clients\Services\ClientsService;
use Modules\Clients\Http\Requests\FilterClientsRequest;
use Throwable;

class ClientsController extends Controller
{
    private $clientService;

    public function __construct()
    {
        $this->clientService = new ClientsService();
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('clients::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('clients::create');
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
        return view('clients::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('clients::edit');
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

    public function filterClients(FilterClientsRequest $input)
    {
        try {
            $searchInput = $input->get('searchInput');

            if (!$searchInput) {
                return response()->json(['message' => 'error', 'error' => 'No input found!'], 404);
            }

            $clients = $this->clientService->getFilteredClients($searchInput);

            if (!$clients) {
                return response()->json(['message' => 'error', 'error' => 'No Clients found!'], 404);
            }
            return $clients;
        } catch (Throwable $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!', 500]);
        }
    }
}
