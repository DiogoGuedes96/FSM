<?php

namespace Modules\Products\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Products\Http\Requests\FilterProductsRequest;
use Modules\Products\Http\Requests\UpdatePriceProductsRequest;
use Modules\Products\Services\BmsProductsService;

class ProductsController extends Controller
{
    /**
     * @var BmsProductsService
     */
    private $bmsProductsService;

    public function __construct()
    {
        $this->bmsProductsService = new BmsProductsService();
    }

    public function index()
    {
        return view('products::index');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getAllProductsPaginate($stock = null)
    {
        try {
            $products = $this->bmsProductsService->getAllProducts($stock);

            if (!$products) {
                return response()->json(['message' => 'error', 'error' => 'No products found!'], 404);
            }
            return  $products;
        } catch (Throwable $th) {
            return response()->json(['message' => 'error', 'error' => 'Try again later!', 500]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('products::create');
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
        return view('products::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('products::edit');
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

    public function getProducts(Request $request)
    {
        return $this->bmsProductsService->getAllProductsManager(
            $request->query('page') ?? 1,
            $request->query('perPage') ?? 20,
            $request->query('search'),
            $request->query('sort'),
            $request->query('sortDirection'),
            $request->query('unit'),
            $request->query('status')
        );
    }

    public function updateProducts(UpdatePriceProductsRequest $request)
    {
        $this->bmsProductsService->updateProducts($request->all());

        return response()->json([
            'message' => 'Products updated successfully'
        ]);
    }

    public function getUnits()
    {
        try {
            return $this->bmsProductsService->getDistinctUnits();
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getProductFamilies($withSubCategories)
    {
        try {
            $withSubCategories = ($withSubCategories === "false") ? false : true;
            $categories = $this->bmsProductsService->getProductCategories($withSubCategories);

            return response()->json(['categories' => $categories]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function filterProducts(FilterProductsRequest $input)
    {
        try {
            $searchInput     = $input->get('searchInput') ?? null;
            $categoryCode    = $input->get('category_code') ?? null;
            $subCategoryCode = $input->get('sub_category_code') ?? null;
            $group           = $input->get('group') ?? null;
            $stock           = $input->get('stock') ?? null;

            if (!$searchInput && !$categoryCode && !$subCategoryCode && !$group) {
                $this->bmsProductsService->getAllProducts($stock);
            }

            $products = $this->bmsProductsService->getFilteredProducts($searchInput, $categoryCode, $subCategoryCode, $group, $stock);

            return $products;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function syncProducts()
    {
        try {
            $exitCode = $this->bmsProductsService->syncProducts();
            return response()->json(['message' => 'Products synced successfully', 'code' => $exitCode]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function syncBatchs()
    {
        try {
            $exitCode = $this->bmsProductsService->syncBatchs();
            return response()->json(['message' => 'Batchs synced successfully', 'code' => $exitCode]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function syncClients()
    {
        try {
            $exitCode = $this->bmsProductsService->syncClients();
            return response()->json(['message' => 'Clients synced successfully', 'code' => $exitCode]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
