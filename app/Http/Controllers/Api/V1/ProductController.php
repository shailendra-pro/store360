<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Get all categories with subcategories and products
     * Only returns products that belong to the authenticated user's company or are global
     */
    public function getCategoriesWithProducts(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Get user's company name
        $companyName = $user->role === 'business' ? $user->business_name : $user->company;

        // Get active categories with their subcategories and products
        $categories = Category::with(['subcategories' => function ($query) use ($companyName) {
            $query->where('is_active', true)
                  ->with(['products' => function ($productQuery) use ($companyName) {
                      $productQuery->where('is_active', true)
                                  ->where(function ($q) use ($companyName) {
                                      $q->where('is_global', true)
                                        ->orWhere('company_id', function ($subQuery) use ($companyName) {
                                            $subQuery->select('id')
                                                    ->from('users')
                                                    ->where('business_name', $companyName)
                                                    ->where('role', 'business');
                                        });
                                  })
                                  ->with(['images' => function ($imageQuery) {
                                      $imageQuery->orderBy('sort_order', 'asc');
                                  }]);
                  }]);
        }])
        ->where('is_active', true)
        ->orderBy('name', 'asc')
        ->get();

        // Transform data to match the required format
        $formattedCategories = $categories->map(function ($category) {
            return [
                'id' => 'cat_' . strtolower(str_replace(' ', '_', $category->name)),
                'name' => $category->name,
                'subcategories' => $category->subcategories->map(function ($subcategory) {
                    return [
                        'id' => 'sub_' . strtolower(str_replace(' ', '_', $subcategory->name)),
                        'name' => $subcategory->name,
                        'products' => $subcategory->products->map(function ($product) {
                            return [
                                'id' => 'prod_' . strtolower(str_replace(' ', '_', $product->title)),
                                'name' => $product->title,
                                'description' => $product->description,
                                'thumbnailUrl' => $product->main_image_url ?: 'https://placehold.co/300x400/FFFFFF/000000?text=' . urlencode($product->title),
                                'imageUrls' => $product->image_urls ?: ['https://placehold.co/800x1000/FFFFFF/000000?text=' . urlencode($product->title)],
                                'price' => $product->formatted_price,
                                'sku' => $product->sku,
                                'stock_quantity' => $product->stock_quantity,
                                'stock_status' => $product->stock_status,
                                'specifications' => $product->specifications,
                                'category_name' => $product->category_name,
                                'subcategory_name' => $product->subcategory_name,
                                'company_name' => $product->company_name,
                                'is_global' => $product->is_global,
                            ];
                        })->values()
                    ];
                })->filter(function ($subcategory) {
                    return count($subcategory['products']) > 0;
                })->values()
            ];
        })->filter(function ($category) {
            return count($category['subcategories']) > 0;
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $formattedCategories
            ]
        ]);
    }

    /**
     * Get all products for the authenticated user's company or global products
     */
    public function getProducts(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Get user's company name
        $companyName = $user->role === 'business' ? $user->business_name : $user->company;

        // Get products that belong to the company or are global
        $products = Product::with(['images' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        }, 'subcategory.category', 'company'])
        ->where('is_active', true)
        ->where(function ($query) use ($companyName) {
            $query->where('is_global', true)
                  ->orWhere('company_id', function ($subQuery) use ($companyName) {
                      $subQuery->select('id')
                              ->from('users')
                              ->where('business_name', $companyName)
                              ->where('role', 'business');
                  });
        })
        ->orderBy('title', 'asc')
        ->get();

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => 'prod_' . strtolower(str_replace(' ', '_', $product->title)),
                'name' => $product->title,
                'description' => $product->description,
                'thumbnailUrl' => $product->main_image_url ?: 'https://placehold.co/300x400/FFFFFF/000000?text=' . urlencode($product->title),
                'imageUrls' => $product->image_urls ?: ['https://placehold.co/800x1000/FFFFFF/000000?text=' . urlencode($product->title)],
                'price' => $product->formatted_price,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'stock_status' => $product->stock_status,
                'specifications' => $product->specifications,
                'category_name' => $product->category_name,
                'subcategory_name' => $product->subcategory_name,
                'company_name' => $product->company_name,
                'is_global' => $product->is_global,
                'category_id' => 'cat_' . strtolower(str_replace(' ', '_', $product->category_name)),
                'subcategory_id' => 'sub_' . strtolower(str_replace(' ', '_', $product->subcategory_name)),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $formattedProducts
            ]
        ]);
    }

    /**
     * Get a specific product by ID
     */
    public function getProduct(Request $request, $productId)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Get user's company name
        $companyName = $user->role === 'business' ? $user->business_name : $user->company;

        // Find product by slug or ID
        $product = Product::with(['images' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        }, 'subcategory.category', 'company'])
        ->where('is_active', true)
        ->where(function ($query) use ($productId) {
            $query->where('id', $productId)
                  ->orWhere('slug', $productId);
        })
        ->where(function ($query) use ($companyName) {
            $query->where('is_global', true)
                  ->orWhere('company_id', function ($subQuery) use ($companyName) {
                      $subQuery->select('id')
                              ->from('users')
                              ->where('business_name', $companyName)
                              ->where('role', 'business');
                  });
        })
        ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $formattedProduct = [
            'id' => 'prod_' . strtolower(str_replace(' ', '_', $product->title)),
            'name' => $product->title,
            'description' => $product->description,
            'thumbnailUrl' => $product->main_image_url ?: 'https://placehold.co/300x400/FFFFFF/000000?text=' . urlencode($product->title),
            'imageUrls' => $product->image_urls ?: ['https://placehold.co/800x1000/FFFFFF/000000?text=' . urlencode($product->title)],
            'price' => $product->formatted_price,
            'sku' => $product->sku,
            'stock_quantity' => $product->stock_quantity,
            'stock_status' => $product->stock_status,
            'specifications' => $product->specifications,
            'category_name' => $product->category_name,
            'subcategory_name' => $product->subcategory_name,
            'company_name' => $product->company_name,
            'is_global' => $product->is_global,
            'category_id' => 'cat_' . strtolower(str_replace(' ', '_', $product->category_name)),
            'subcategory_id' => 'sub_' . strtolower(str_replace(' ', '_', $product->subcategory_name)),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $formattedProduct
            ]
        ]);
    }

    /**
     * Get categories only
     */
    public function getCategories(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $categories = Category::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $formattedCategories = $categories->map(function ($category) {
            return [
                'id' =>$category->id,
                'name' => $category->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $formattedCategories
            ]
        ]);
    }

    /**
     * Get subcategories for a specific category
     */
    public function getSubcategories(Request $request, $categoryId = null)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $query = Subcategory::where('is_active', true);

        if ($categoryId) {
            // Remove 'cat_' prefix if present
            $categoryName = str_replace('cat_', '', $categoryId);
            $categoryName = str_replace('_', ' ', $categoryName);
            $categoryName = ucwords($categoryName);

            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        $subcategories = $query->with('category')
            ->orderBy('name', 'asc')
            ->get();

        $formattedSubcategories = $subcategories->map(function ($subcategory) {
            return [
                'id' => 'sub_' . strtolower(str_replace(' ', '_', $subcategory->name)),
                'name' => $subcategory->name,
                'category_id' => 'cat_' . strtolower(str_replace(' ', '_', $subcategory->category->name)),
                'category_name' => $subcategory->category->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'subcategories' => $formattedSubcategories
            ]
        ]);
    }
}
