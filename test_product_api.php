<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

// Test configuration
$baseUrl = 'http://localhost:8000/api/v1';
$testCredentials = [
    'business' => [
        'username' => 'techsolutions',
        'password' => 'password123'
    ],
    'end_user' => [
        'username' => 'johnsmith',
        'password' => 'password123'
    ]
];

echo "🧪 Testing Product API Endpoints\n";
echo "================================\n\n";

// Test login for business user
echo "1. Testing Business User Login...\n";
$loginResponse = Http::post($baseUrl . '/business/login', $testCredentials['business']);

if ($loginResponse->successful()) {
    $loginData = $loginResponse->json();
    $token = $loginData['data']['token'];
    echo "✅ Business login successful\n";
    echo "   Role: " . $loginData['data']['role'] . "\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Business login failed: " . $loginResponse->body() . "\n\n";
    exit(1);
}

// Test headers for authenticated requests
$headers = [
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
];

// Test 2: Get categories with products
echo "2. Testing Categories with Products...\n";
$categoriesResponse = Http::withHeaders($headers)
    ->get($baseUrl . '/products/categories-with-products');

if ($categoriesResponse->successful()) {
    $categoriesData = $categoriesResponse->json();
    echo "✅ Categories with products retrieved successfully\n";
    echo "   Categories count: " . count($categoriesData['data']['categories']) . "\n";
    
    if (count($categoriesData['data']['categories']) > 0) {
        $firstCategory = $categoriesData['data']['categories'][0];
        echo "   First category: " . $firstCategory['name'] . " (ID: " . $firstCategory['id'] . ")\n";
        echo "   Subcategories count: " . count($firstCategory['subcategories']) . "\n";
        
        if (count($firstCategory['subcategories']) > 0) {
            $firstSubcategory = $firstCategory['subcategories'][0];
            echo "   First subcategory: " . $firstSubcategory['name'] . " (ID: " . $firstSubcategory['id'] . ")\n";
            echo "   Products count: " . count($firstSubcategory['products']) . "\n";
            
            if (count($firstSubcategory['products']) > 0) {
                $firstProduct = $firstSubcategory['products'][0];
                echo "   First product: " . $firstProduct['name'] . " (ID: " . $firstProduct['id'] . ")\n";
                echo "   Price: " . $firstProduct['price'] . "\n";
                echo "   Stock: " . $firstProduct['stock_quantity'] . "\n";
            }
        }
    }
    echo "\n";
} else {
    echo "❌ Categories with products failed: " . $categoriesResponse->body() . "\n\n";
}

// Test 3: Get all products
echo "3. Testing All Products...\n";
$productsResponse = Http::withHeaders($headers)
    ->get($baseUrl . '/products/all');

if ($productsResponse->successful()) {
    $productsData = $productsResponse->json();
    echo "✅ All products retrieved successfully\n";
    echo "   Products count: " . count($productsData['data']['products']) . "\n";
    
    if (count($productsData['data']['products']) > 0) {
        $firstProduct = $productsData['data']['products'][0];
        echo "   First product: " . $firstProduct['name'] . " (ID: " . $firstProduct['id'] . ")\n";
        echo "   Category: " . $firstProduct['category_name'] . "\n";
        echo "   Subcategory: " . $firstProduct['subcategory_name'] . "\n";
        echo "   Company: " . $firstProduct['company_name'] . "\n";
        echo "   Is Global: " . ($firstProduct['is_global'] ? 'Yes' : 'No') . "\n";
    }
    echo "\n";
} else {
    echo "❌ All products failed: " . $productsResponse->body() . "\n\n";
}

// Test 4: Get categories only
echo "4. Testing Categories Only...\n";
$categoriesOnlyResponse = Http::withHeaders($headers)
    ->get($baseUrl . '/products/categories');

if ($categoriesOnlyResponse->successful()) {
    $categoriesOnlyData = $categoriesOnlyResponse->json();
    echo "✅ Categories only retrieved successfully\n";
    echo "   Categories count: " . count($categoriesOnlyData['data']['categories']) . "\n";
    
    foreach ($categoriesOnlyData['data']['categories'] as $category) {
        echo "   - " . $category['name'] . " (ID: " . $category['id'] . ")\n";
    }
    echo "\n";
} else {
    echo "❌ Categories only failed: " . $categoriesOnlyResponse->body() . "\n\n";
}

// Test 5: Get subcategories for a specific category
if (isset($categoriesOnlyData['data']['categories'][0])) {
    $firstCategoryId = $categoriesOnlyData['data']['categories'][0]['id'];
    echo "5. Testing Subcategories for Category: " . $firstCategoryId . "...\n";
    
    $subcategoriesResponse = Http::withHeaders($headers)
        ->get($baseUrl . '/products/subcategories/' . $firstCategoryId);

    if ($subcategoriesResponse->successful()) {
        $subcategoriesData = $subcategoriesResponse->json();
        echo "✅ Subcategories retrieved successfully\n";
        echo "   Subcategories count: " . count($subcategoriesData['data']['subcategories']) . "\n";
        
        foreach ($subcategoriesData['data']['subcategories'] as $subcategory) {
            echo "   - " . $subcategory['name'] . " (ID: " . $subcategory['id'] . ")\n";
        }
        echo "\n";
    } else {
        echo "❌ Subcategories failed: " . $subcategoriesResponse->body() . "\n\n";
    }
}

// Test 6: Get specific product
if (isset($productsData['data']['products'][0])) {
    $firstProductId = $productsData['data']['products'][0]['id'];
    echo "6. Testing Specific Product: " . $firstProductId . "...\n";
    
    $productResponse = Http::withHeaders($headers)
        ->get($baseUrl . '/products/' . $firstProductId);

    if ($productResponse->successful()) {
        $productData = $productResponse->json();
        echo "✅ Specific product retrieved successfully\n";
        $product = $productData['data']['product'];
        echo "   Name: " . $product['name'] . "\n";
        echo "   Description: " . substr($product['description'], 0, 50) . "...\n";
        echo "   Price: " . $product['price'] . "\n";
        echo "   SKU: " . $product['sku'] . "\n";
        echo "   Stock: " . $product['stock_quantity'] . "\n";
        echo "   Images count: " . count($product['imageUrls']) . "\n";
        echo "\n";
    } else {
        echo "❌ Specific product failed: " . $productResponse->body() . "\n\n";
    }
}

// Test 7: Test with end user login
echo "7. Testing End User Login and Products...\n";
$endUserLoginResponse = Http::post($baseUrl . '/business/login', $testCredentials['end_user']);

if ($endUserLoginResponse->successful()) {
    $endUserLoginData = $endUserLoginResponse->json();
    $endUserToken = $endUserLoginData['data']['token'];
    echo "✅ End user login successful\n";
    echo "   Role: " . $endUserLoginData['data']['role'] . "\n";
    echo "   Company: " . $endUserLoginData['data']['user']['company'] . "\n\n";
    
    // Test products with end user token
    $endUserHeaders = [
        'Authorization' => 'Bearer ' . $endUserToken,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];
    
    $endUserProductsResponse = Http::withHeaders($endUserHeaders)
        ->get($baseUrl . '/products/all');
    
    if ($endUserProductsResponse->successful()) {
        $endUserProductsData = $endUserProductsResponse->json();
        echo "✅ End user products retrieved successfully\n";
        echo "   Products count: " . count($endUserProductsData['data']['products']) . "\n";
        
        // Check if products are filtered by company
        $companyProducts = array_filter($endUserProductsData['data']['products'], function($product) use ($endUserLoginData) {
            return $product['company_name'] === $endUserLoginData['data']['user']['company'] || $product['is_global'];
        });
        
        echo "   Company/Global products: " . count($companyProducts) . "\n";
        echo "   All products are company-specific or global: " . (count($companyProducts) === count($endUserProductsData['data']['products']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ End user products failed: " . $endUserProductsResponse->body() . "\n";
    }
} else {
    echo "❌ End user login failed: " . $endUserLoginResponse->body() . "\n";
}

echo "\n🎉 Product API Testing Complete!\n"; 