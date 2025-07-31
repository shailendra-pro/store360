<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\BusinessAuthController;
use App\Http\Controllers\BusinessController as CompanyController;

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/password/update', [AdminController::class, 'updatePassword'])->name('admin.password.update');

        // Category Management
        Route::resource('categories', CategoryController::class, ['as' => 'admin']);
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');

        // User Management
        Route::resource('users', UserController::class, ['as' => 'admin']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::post('users/{user}/generate-secure-link', [UserController::class, 'generateSecureLink'])->name('admin.users.generate-secure-link');
        Route::post('users/{user}/extend-secure-link', [UserController::class, 'extendSecureLink'])->name('admin.users.extend-secure-link');
        Route::delete('users/{user}/revoke-secure-link', [UserController::class, 'revokeSecureLink'])->name('admin.users.revoke-secure-link');
        Route::get('users/secure-link-stats', [UserController::class, 'secureLinkStats'])->name('admin.users.secure-link-stats');

        // Subcategories Management
        Route::resource('subcategories', SubcategoryController::class, ['as' => 'admin']);
        Route::patch('subcategories/{subcategory}/toggle-status', [SubcategoryController::class, 'toggleStatus'])->name('admin.subcategories.toggle-status');

        // Business Management
        Route::resource('businesses', BusinessController::class, ['as' => 'admin']);
        Route::patch('businesses/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('admin.businesses.toggle-status');
        Route::post('businesses/{business}/update-credentials', [BusinessController::class, 'updateCredentials'])->name('admin.businesses.update-credentials');
        Route::post('businesses/{business}/update-logo', [BusinessController::class, 'updateLogo'])->name('admin.businesses.update-logo');

        // Product Management
        Route::resource('products', ProductController::class, ['as' => 'admin']);
        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
        Route::patch('products/images/{image}/set-primary', [ProductController::class, 'setPrimaryImage'])->name('admin.products.set-primary-image');
        Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('admin.products.delete-image');

        Route::get('/', function () {
            return redirect('/admin/dashboard');
        });
    });
});

// Business Authentication Routes
Route::prefix('business')->group(function () {
    Route::get('/login', [BusinessAuthController::class, 'showLogin'])->name('business.login');
    Route::post('/login', [BusinessAuthController::class, 'login'])->name('business.login.post');
    Route::post('/logout', [BusinessAuthController::class, 'logout'])->name('business.logout');

    // Protected Business Routes
    Route::middleware(['auth', 'business'])->group(function () {
        Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('business.dashboard');

        // User Management
        Route::get('/users', [CompanyController::class, 'users'])->name('business.users.index');
        Route::get('/users/create', [CompanyController::class, 'createUser'])->name('business.users.create');
        Route::post('/users', [CompanyController::class, 'storeUser'])->name('business.users.store');
        Route::get('/users/{id}', [CompanyController::class, 'showUser'])->name('business.users.show');
        Route::patch('/users/{id}/toggle-status', [CompanyController::class, 'toggleUserStatus'])->name('business.users.toggle-status');
        Route::post('/users/{id}/generate-secure-link', [CompanyController::class, 'generateSecureLink'])->name('business.users.generate-secure-link');
        Route::post('/users/{id}/extend-secure-link', [CompanyController::class, 'extendSecureLink'])->name('business.users.extend-secure-link');

        // Logo Management
        Route::get('/logo', [CompanyController::class, 'logo'])->name('business.logo.index');
        Route::post('/logo', [CompanyController::class, 'updateLogo'])->name('business.logo.update');
        Route::delete('/logo', [CompanyController::class, 'deleteLogo'])->name('business.logo.delete');
    });
});

// Secure Link Route
Route::get('/secure/{secure_link}', function ($secure_link) {
    $user = \App\Models\User::where('secure_link', $secure_link)->first();

    if (!$user || !$user->isSecureLinkValid()) {
        abort(404, 'Secure link not found or expired.');
    }

    return view('secure.access', compact('user'));
})->name('secure.access');

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/admin/login');
});
