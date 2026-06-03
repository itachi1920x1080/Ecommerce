<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController; // 🟢 បានបន្ថែម UserController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ១. អ្នកធម្មតាអាចមើលទំនិញបាន (Public Routes)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);
Route::get('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'index']);
Route::get('/products/{product}/variants', [\App\Http\Controllers\ProductVariantController::class, 'index']);

// Telegram Webhook
Route::post('/webhook', [TelegramWebhookController::class, 'handle']);

// Payment & Order Status Checking (Public)
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook']);
Route::get('/orders/{id}/status', [\App\Http\Controllers\OrderController::class, 'checkStatus']);

// ២. បង្កើតគណនី (Register API)
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|min:8',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user', // លំនាំដើមជា user
    ]);

    return response()->json([
        'message' => 'បង្កើតគណនីជោគជ័យ!',
        'token' => $user->createToken('API_Token')->plainTextToken
    ], 201);
});

// ៣. ចូលគណនី (Login API)
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Email ឬ Password មិនត្រឹមត្រូវទេ'], 401);
    }

    return response()->json([
        'message' => 'ចូលគណនីជោគជ័យ!',
        'token' => $user->createToken('API_Token')->plainTextToken,
        'role' => $user->role 
    ]);
});


// ៤. ក្រុមទី ១៖ សម្រាប់អ្នកប្រើប្រាស់ធម្មតា (User Routes)
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ចាកចេញជោគជ័យ!']);
    });
    
    // 📦 មើលប្រវត្តិការទិញរបស់ខ្លួនឯង
    Route::get('/my-orders', [\App\Http\Controllers\OrderController::class, 'myOrders']);

    // កន្ត្រកទិញទំនិញ និង គិតលុយ
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart', [\App\Http\Controllers\CartController::class, 'store']);
    Route::delete('/cart/{id}', [\App\Http\Controllers\CartController::class, 'destroy']);
    Route::post('/cart/checkout', [\App\Http\Controllers\OrderController::class, 'checkout']);
    
    // បញ្ចូលការវាយតម្លៃទំនិញ (តម្រូវឱ្យ Login)
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store']);
    
    // ផ្ទៀងផ្ទាត់កូដបញ្ចុះតម្លៃ (Coupons)
    Route::post('/coupons/verify', [\App\Http\Controllers\CouponController::class, 'verify']);
    
    // ❤️ ប្រព័ន្ធទំនិញចំណូលចិត្ត (Wishlist)
    Route::get('/wishlists', [\App\Http\Controllers\WishlistController::class, 'index']);
    Route::post('/wishlists/{productId}/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle']);
    // សម្រាប់អ្នកប្រើប្រាស់កែប្រែប្រវត្តិរូបខ្លួនឯង
    Route::put('/user/profile', [\App\Http\Controllers\UserController::class, 'updateProfile']);
    // 📍 គ្រប់គ្រងអាសយដ្ឋានដឹកជញ្ជូន
    Route::get('/addresses', [\App\Http\Controllers\AddressController::class, 'index']);
    Route::post('/addresses', [\App\Http\Controllers\AddressController::class, 'store']);
    Route::delete('/addresses/{id}', [\App\Http\Controllers\AddressController::class, 'destroy']);
});

// ៥. ក្រុមទី ២៖ សម្រាប់តែម្ចាស់ហាងប៉ុណ្ណោះ (Admin Routes) 🛡️
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    // 📊 ទាញយករបាយការណ៍ Dashboard
    Route::get('/dashboard/analytics', [\App\Http\Controllers\DashboardController::class, 'getAnalytics']);

   // 👥 🟢 គ្រប់គ្រងអ្នកប្រើប្រាស់ (Admin Manage Users)
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::put('/admin/users/{id}', [UserController::class, 'update']);
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);

    // 🛍️ គ្រប់គ្រងទំនិញ (Products)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    // គ្រប់គ្រងជម្រើសទំនិញ (Variants)
    Route::post('/products/{product}/variants', [\App\Http\Controllers\ProductVariantController::class, 'store']);
    Route::delete('/variants/{id}', [\App\Http\Controllers\ProductVariantController::class, 'destroy']);

    // គ្រប់គ្រង Categories
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store']);
    Route::delete('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'destroy']);
    
    // 📦 គ្រប់គ្រងវិក្កយបត្រអតិថិជន (Orders)
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index']);
    Route::put('/orders/{id}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus']);
    
    // គ្រប់គ្រងគូប៉ុងបញ្ចុះតម្លៃ
    Route::get('/coupons', [\App\Http\Controllers\CouponController::class, 'index']);
    Route::post('/coupons', [\App\Http\Controllers\CouponController::class, 'store']);
    Route::delete('/coupons/{id}', [\App\Http\Controllers\CouponController::class, 'destroy']);
    
});

// ៦. ក្រុមទី ៣៖ សម្រាប់តែអ្នកដឹកជញ្ជូន (Driver Routes) 🛵
Route::middleware(['auth:sanctum', 'driver'])->group(function () {
    Route::get('/driver/available-orders', [\App\Http\Controllers\DriverController::class, 'getAvailableOrders']);
    Route::post('/driver/orders/{id}/accept', [\App\Http\Controllers\DriverController::class, 'acceptOrder']);
    Route::put('/driver/orders/{id}/status', [\App\Http\Controllers\DriverController::class, 'updateStatus']);
});