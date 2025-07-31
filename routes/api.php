<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BusinessAuthController;
use App\Http\Controllers\Api\V1\BusinessController;

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

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Business Authentication Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('business')->group(function () {
        // Public routes (no authentication required)
        Route::post('/login', [BusinessAuthController::class, 'login']);
        Route::post('/check-auth', [BusinessAuthController::class, 'checkAuth']);
    });

    /*
    |--------------------------------------------------------------------------
    | Business Protected Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('business')->middleware(['auth:sanctum', 'api.business'])->group(function () {

        // Authentication & Profile
        Route::post('/logout', [BusinessAuthController::class, 'logout']);
        Route::get('/profile', [BusinessAuthController::class, 'profile']);
        Route::put('/profile', [BusinessAuthController::class, 'updateProfile']);
        Route::post('/change-password', [BusinessAuthController::class, 'changePassword']);
        Route::post('/refresh-token', [BusinessAuthController::class, 'refresh']);

        // Dashboard & Statistics
        Route::get('/dashboard', [BusinessController::class, 'dashboard']);
        Route::get('/statistics', [BusinessController::class, 'getStatistics']);
        Route::get('/companies', [BusinessController::class, 'getCompanies']);
        Route::get('/expiring-links', [BusinessController::class, 'getExpiringLinks']);

        // User Management
        Route::get('/users', [BusinessController::class, 'getUsers']);
        Route::get('/users/{userId}', [BusinessController::class, 'getUser']);

        // Business Settings
        Route::post('/update-logo', [BusinessController::class, 'updateLogo']);
    });
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
