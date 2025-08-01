<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\WebGLAuthService;
use Carbon\Carbon;

class WebGLController extends Controller
{
    protected WebGLAuthService $authService;

    public function __construct(WebGLAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Validate secure link and get store information
     */
    public function validateSecureLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secure_link' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->authService->validateSecureLink($request->secure_link);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        $user = $result['user'];
        $company = $result['company'];

        return response()->json([
            'success' => true,
            'message' => 'Secure link is valid',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->business_name,
                    'email' => $user->email ?? $user->contact_email,
                    'company' => $user->company ?? $user->business_name,
                    'role' => $user->role,
                    'expires_at' => $user->expires_at,
                    'remaining_hours' => $user->remaining_hours,
                ],
                'company' => [
                    'id' => $company->id,
                    'business_name' => $company->business_name,
                    'logo_url' => $company->logo_url,
                    'contact_email' => $company->contact_email,
                ],
                'store_config' => [
                    'store_name' => $company->business_name,
                    'logo_url' => $company->logo_url,
                    'theme_color' => '#0d6efd',
                    'splash_duration' => 3000,
                ]
            ]
        ]);
    }

        /**
     * Authenticate user login for WebGL application
     * Supports both business users and end users
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->authService->authenticate(
            $request->email,
            $request->password
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        $user = $result['user'];
        $company = $result['company'];
        $userType = $result['user_type'];
        $sessionToken = $result['session_token'];
        $sessionExpiry = $result['session_expiry'];

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'session_token' => $sessionToken,
                'user_type' => $userType,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->business_name,
                    'email' => $user->email ?? $user->contact_email,
                    'company' => $user->company ?? $user->business_name,
                ],
                'company' => [
                    'id' => $company->id,
                    'business_name' => $company->business_name,
                    'logo_url' => $company->logo_url,
                    'contact_email' => $company->contact_email,
                ],
                'store_config' => [
                    'store_name' => $company->business_name,
                    'logo_url' => $company->logo_url,
                    'theme_color' => '#0d6efd',
                    'splash_duration' => 3000,
                ],
                'session_expires_at' => $sessionExpiry->toISOString(),
                'access_level' => $userType === 'business' ? 'admin' : 'user'
            ]
        ]);
    }

    /**
     * Validate session token
     */
    public function validateSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->authService->validateSession($request->session_token);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        $user = $result['user'];
        $company = $result['company'];
        $sessionData = $result['session_data'];

        return response()->json([
            'success' => true,
            'message' => 'Session is valid',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->business_name,
                    'email' => $user->email ?? $user->contact_email,
                    'company' => $user->company ?? $user->business_name,
                ],
                'company' => [
                    'id' => $company->id,
                    'business_name' => $company->business_name,
                    'logo_url' => $company->logo_url,
                ],
                'session_expires_at' => $sessionData['expires_at']
            ]
        ]);
    }

    /**
     * Logout and invalidate session
     */
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $this->authService->logout($request->session_token);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get store products for WebGL
     */
    public function getProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $sessionData = \Illuminate\Support\Facades\Cache::get("webgl_session_{$request->session_token}");

        if (!$sessionData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired session'
            ], 401);
        }

        $company = User::find($sessionData['company_id']);

        // Get products for this company (both company-specific and global)
        $products = \App\Models\Product::where('is_active', true)
            ->where(function($query) use ($company) {
                $query->where('company_id', $company->id)
                      ->orWhere('is_global', true);
            })
            ->with(['subcategory.category', 'images', 'primaryImage'])
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'sku' => $product->sku,
                    'stock_quantity' => $product->stock_quantity,
                    'stock_status' => $product->stock_status,
                    'category' => [
                        'id' => $product->subcategory->category->id,
                        'name' => $product->subcategory->category->name,
                    ],
                    'subcategory' => [
                        'id' => $product->subcategory->id,
                        'name' => $product->subcategory->name,
                    ],
                    'images' => $product->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->image_url,
                            'thumbnail_url' => $image->thumbnail_url,
                            'is_primary' => $image->is_primary,
                            'alt_text' => $image->alt_text,
                        ];
                    }),
                    'main_image' => $product->main_image_url,
                    'specifications' => $product->specifications,
                    'is_global' => $product->is_global,
                    'created_at' => $product->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => [
                'products' => $products,
                'total_count' => $products->count(),
                'company_name' => $company->business_name,
            ]
        ]);
    }

    /**
     * Get store categories for WebGL
     */
    public function getCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $sessionData = \Illuminate\Support\Facades\Cache::get("webgl_session_{$request->session_token}");

        if (!$sessionData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired session'
            ], 401);
        }

        $categories = \App\Models\Category::where('is_active', true)
            ->with(['subcategories' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'subcategories' => $category->subcategories->map(function($subcategory) {
                        return [
                            'id' => $subcategory->id,
                            'name' => $subcategory->name,
                            'slug' => $subcategory->slug,
                            'description' => $subcategory->description,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => [
                'categories' => $categories,
                'total_count' => $categories->count(),
            ]
        ]);
    }
}
