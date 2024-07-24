<?php

namespace Modules\Products\Services;

use Exception;
use Modules\Products\Entities\BmsProductPrices;
use Modules\Products\Services\BmsProductImagesService;
use Modules\Products\Entities\Products as ProductsEntity;
use Illuminate\Pagination\LengthAwarePaginator as PaginationClass;
use Illuminate\Support\Facades\Artisan;
use Modules\Products\Entities\BmsPoductCategories;
use Modules\Products\Entities\BmsPoductSubCategories;

class BmsProductsService
{

    private $bmsProduct;
    private $bmsProductImagesService;
    private $bmsProductCategory;
    private $bmsProductSubCategory;

    const STATUS_OPTIONS = [
        'activo' => 1,
        'inactivo' => 0
    ];

    public function __construct()
    {
        $this->bmsProduct              = new ProductsEntity();
        $this->bmsProductImagesService = new BmsProductImagesService();
        $this->bmsProductCategory      = new BmsPoductCategories();
        $this->bmsProductSubCategory   = new BmsPoductSubCategories();
    }

    /**
     * This function retrieves a BMS product by its ERP product ID.
     *
     * @param primaveraId The parameter is a variable that represents the ID of a product in
     * the Primavera ERP system. The function `getBmsProductByPrimaveraId` uses this ID to retrieve the
     * corresponding product from the `bmsProduct` collection.
     *
     * @return a single BMS product that matches the given Primavera ID.
     */
    public function getBmsProductByPrimaveraId($primaveraId)
    {
        return $this->bmsProduct->where('erp_product_id', $primaveraId)->first();
    }

    public function getDistinctUnits()
    {
        $bmsProductPrices = new BmsProductPrices();

        return $bmsProductPrices->distinct()->pluck('unit');
    }

    public function getAllProductsManager(
        $page = 1,
        $perPage = 40,
        $search = null,
        $sort = null,
        $sortDirection = null,
        $unit = null,
        $status = null
    ) {
        $query = $this->bmsProduct
            ->with('prices', 'images', 'batches');

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('erp_product_id', 'like', '%' . $search . '%');
            });
        }

        if (!empty($sort) && !empty($sortDirection)) {
            if ($sort == 'current_stock') {
                if ($sortDirection == 'asc') {
                    $query->whereHas('batches', function ($query) use ($sortDirection) {
                        $query->where('quantity', '>', 0);
                    });
                }
                if ($sortDirection == 'desc') {
                    $query->whereDoesntHave('batches');
                }
            } else {
                $query->orderBy($sort, $sortDirection);
            }
        }

        if (!empty($unit)) {
            $query->whereHas('prices', function ($query) use ($unit) {
                $query->whereIn('unit', explode(',', $unit));
            });
        }

        if (!empty($status)) {
            $query->where('active', '=', self::STATUS_OPTIONS[$status]);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function updateProducts($products)
    {

        $imageUrl = null;
        $deleteImage = false;

        if (!empty($products['image'])) {
            $imageUrl = $this->bmsProductImagesService->saveProductImage($products['image']);
        }

        if (!empty($products['deleteImage'])) {
            $deleteImage = true;
        }

        foreach ($products['ids'] as $value) {
            $product = $this->bmsProduct->find($value);

            if ($imageUrl) {
                $product->images()->delete();

                $product->images()->create([
                    'image_url' => $imageUrl
                ]);
            }

            if ($deleteImage) {
                $product->images()->delete();
            }

            if (isset($products['active'])) {
                $product->active = $products['active'];
                $product->save();
            }
        }
    }

    private function splitProperties($obj)
    {
        $result = array();
        $numProps = count($obj) / 2;

        for ($i = 0; $i < $numProps; $i++) {
            $priceKey = "price-$i";
            $unitKey = "unit-$i";
            $defaultKey = "default-$i";

            if (array_key_exists($priceKey, $obj) && array_key_exists($unitKey, $obj)) {
                $price = $obj[$priceKey];
                $unit = $obj[$unitKey];
                $default = !empty($obj[$defaultKey]) ? $obj[$defaultKey] : 0;

                $result[] = array('price' => $price, 'unit' => $unit, 'default' => $default);
            }
        }

        return $result;
    }

    public function getAllProducts($stock)
    {
        try {
            $products = ProductsEntity::query();

            if ($stock) {
                if ($stock == 'inStock') {
                    $products->whereHas('batches', function ($query) {
                        $query->where('active', 1)
                            ->where('quantity', '>', 0);
                    });
                } elseif ($stock == 'outStock') {
                    $products->whereHas('batches', function ($query) {
                        $query->where('quantity', '<=', 0)
                            ->orWhereNull('quantity');
                    });
                }
            }

            $products = $products->where('active', true)->with('prices', 'images', 'batches')->paginate(40);

            if ($products instanceof PaginationClass) {
                $pagination = [
                    'currentPage' => $products->currentPage(),
                    'perPage' => $products->perPage(),
                    'total' => $products->total(),
                    'lastPage' => $products->lastPage(),
                ];

                return response()->json(
                    array_merge(['message' => 'Products', 'products' => $products->items(), 'pagination' => $pagination])
                );
            }

            return $products;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getFilteredProducts($searchInput, $categoryCode, $subCategoryCode, $group, $stock)
    {
        try {
            $products = ProductsEntity::query();


            // if ($stock == 'inStock') {
            //     $products->where('current_stock', '>', 0);
            // } elseif ($stock == 'outStock') {
            //     $products->where('current_stock', '<=', 0);
            // }

            if ($stock == 'inStock') {
                $products->whereHas('batches', function ($query) {
                    $query->where('active', 1)
                        ->where('quantity', '>', 0);
                });
            } elseif ($stock == 'outStock') {
                $products->where(function($query) {
                    $query->whereHas('batches', function ($subquery) {
                        $subquery->where('quantity', '<=', 0)
                            ->orWhereNull('quantity');
                    })->orWhere('current_stock', '<=', 0);
                });
            }

            if ($searchInput) {
                $products->where(function ($query) use ($searchInput) {
                    $query->where('name', 'like', "%$searchInput%")
                        ->orWhere('erp_product_id', 'like', "%$searchInput%");
                });
            }

            if ($categoryCode) {
                $products->where('category_code', $categoryCode);
            }

            if ($subCategoryCode) {
                $products->where('sub_category_code', $subCategoryCode);
            }

            if ($group) {
                $categories = $this->bmsProductCategory
                    ->where('group', $group)
                    ->pluck('erp_category_code', 'id')
                    ->toArray();

                $categoryIds   = array_keys($categories);
                $categoryCodes = array_values($categories);

                if ($categoryIds) {
                    $subCategoryCodes = $this->bmsProductSubCategory
                        ->whereIn('category', $categoryIds)
                        ->pluck('erp_sub_category_code')
                        ->toArray();

                    $products->whereIn('category_code', $categoryCodes);

                    if ($subCategoryCodes) {
                        $products->whereIn('sub_category_code', $subCategoryCodes);
                    }
                }
            }
            $products = $products->where('active', '!=', 0)->with('prices', 'images', 'batches')->paginate(40);

            if (empty($products)) {
                throw new exception('No products found with given parameters !', 404);
            }

            if ($products instanceof PaginationClass) {
                $pagination = [
                    'currentPage' => $products->currentPage(),
                    'perPage' => $products->perPage(),
                    'total' => $products->total(),
                    'lastPage' => $products->lastPage(),
                ];

                return response()->json(
                    array_merge(['message' => 'Products', 'products' => $products->items(), 'pagination' => $pagination])
                );
            }

            return $products;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getProductCategories($withSubCategories = false)
    {
        try {
            $categories = $this->bmsProductCategory
                ->with('subCategories')
                ->get()
                ->groupBy('group');

            return $categories->map(function ($categories, $group) use ($withSubCategories) {
                $groupData = [
                    'group' => $group,
                    'categories' => $categories->map(function ($category) use ($withSubCategories) {
                        $categoryData = [
                            'id' => $category->id,
                            'name' => $category->name,
                            'erpCategoryCode' => $category->erp_category_code
                        ];

                        if ($withSubCategories) {
                            $categoryData['subCategories'] = $category->subCategories->map(function ($subCategory) {
                                return [
                                    'id' => $subCategory->id,
                                    'name' => $subCategory->name,
                                    'erpSubCategoryCode' => $subCategory->erp_sub_category_code,
                                ];
                            });
                        }

                        return $categoryData;
                    }),
                ];

                return $groupData;
            })->values()->all();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function syncProducts()
    {
        $exitCode = Artisan::call('primavera:products:updateorcreate');

        return $exitCode;
    }

    public function syncBatchs()
    {
        $exitCode = Artisan::call('primavera:batchs:updateorcreate');

        return $exitCode;
    }

    public function syncClients()
    {
        $exitCode = Artisan::call('primavera:clients:updateorcreate');

        return $exitCode;
    }
}
