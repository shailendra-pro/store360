<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BusinessAuthController;
use App\Http\Controllers\Api\V1\BusinessController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\WebGLController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API v1 routes
Route::prefix('v1')->group(function () {
    // Public business routes
    Route::post('business/login', [BusinessAuthController::class, 'login']);
    Route::post('business/register', [BusinessAuthController::class, 'register']);

    // Protected business routes
    Route::middleware(['auth:sanctum'])->prefix('business')->group(function () {
        Route::post('logout', [BusinessAuthController::class, 'logout']);
        Route::get('profile', [BusinessAuthController::class, 'profile']);
        Route::put('profile', [BusinessAuthController::class, 'updateProfile']);
        Route::put('change-password', [BusinessAuthController::class, 'changePassword']);
        Route::post('refresh', [BusinessAuthController::class, 'refresh']);
        Route::get('check-auth', [BusinessAuthController::class, 'checkAuth']);

        Route::get('dashboard', [BusinessController::class, 'dashboard']);
        Route::get('users', [BusinessController::class, 'getUsers']);
        Route::get('users/{user}', [BusinessController::class, 'getUser']);
        Route::put('logo', [BusinessController::class, 'updateLogo']);
        Route::get('statistics', [BusinessController::class, 'getStatistics']);
        Route::get('companies', [BusinessController::class, 'getCompanies']);
        Route::get('expiring-links', [BusinessController::class, 'getExpiringLinks']);
    });

    // Protected product routes (requires authentication)
    Route::middleware(['auth:sanctum'])->prefix('products')->group(function () {
        Route::get('categories-with-products', [ProductController::class, 'getCategoriesWithProducts']);
        Route::get('all', [ProductController::class, 'getProducts']);
        Route::get('categories', [ProductController::class, 'getCategories']);
        Route::get('subcategories/{categoryId?}', [ProductController::class, 'getSubcategories']);
        Route::get('{productId}', [ProductController::class, 'getProduct']);
    });

    // WebGL Application Routes
    Route::post('webgl/validate-link', [WebGLController::class, 'validateSecureLink']);
    Route::post('webgl/login', [WebGLController::class, 'login']);
    Route::post('webgl/validate-session', [WebGLController::class, 'validateSession']);
    Route::post('webgl/logout', [WebGLController::class, 'logout']);
    Route::get('webgl/products', [WebGLController::class, 'getProducts']);
    Route::get('webgl/categories', [WebGLController::class, 'getCategories']);
});

/*
|--------------------------------------------------------------------------
| API Documentation Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Store 360 API',
        'version' => '1.0.0',
        'documentation' => [
            'business_auth' => [
                'login' => 'POST /api/v1/business/login',
                'logout' => 'POST /api/v1/business/logout',
                'profile' => 'GET /api/v1/business/profile',
                'update_profile' => 'PUT /api/v1/business/profile',
                'change_password' => 'POST /api/v1/business/change-password',
                'refresh_token' => 'POST /api/v1/business/refresh-token',
                'check_auth' => 'POST /api/v1/business/check-auth',
            ],
            'business_operations' => [
                'dashboard' => 'GET /api/v1/business/dashboard',
                'statistics' => 'GET /api/v1/business/statistics',
                'companies' => 'GET /api/v1/business/companies',
                'expiring_links' => 'GET /api/v1/business/expiring-links',
                'users' => 'GET /api/v1/business/users',
                'user_details' => 'GET /api/v1/business/users/{userId}',
                'update_logo' => 'POST /api/v1/business/update-logo',
            ],
            'webgl_application' => [
                'validate_link' => 'POST /api/v1/webgl/validate-link',
                'login' => 'POST /api/v1/webgl/login',
                'validate_session' => 'POST /api/v1/webgl/validate-session',
                'logout' => 'POST /api/v1/webgl/logout',
                'products' => 'GET /api/v1/webgl/products',
                'categories' => 'GET /api/v1/webgl/categories',
            ],
            'product_api' => [
                'categories_with_products' => 'GET /api/v1/products/categories-with-products',
                'all_products' => 'GET /api/v1/products/all',
                'categories' => 'GET /api/v1/products/categories',
                'subcategories' => 'GET /api/v1/products/subcategories/{categoryId?}',
                'product_details' => 'GET /api/v1/products/{productId}',
            ]
        ]
    ]);
});

// create fallback route
Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found',
    ], 404);
});
