<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ១. អ្នកធម្មតាអាចមើលទំនិញបាន
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
// ទាញយកប្រភេទ Categories ទាំងអស់ (យកទៅប្រើនៅ Frontend)
Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);
// មើលមតិយោបល់របស់ទំនិញ
Route::get('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'index']);
// មើលជម្រើសផ្សេងៗរបស់ទំនិញ (Variants)
Route::get('/products/{product}/variants', [\App\Http\Controllers\ProductVariantController::class, 'index']);

// 🟢 នេះគឺជាចំណុចដែលបានកែសម្រួល (កែឈ្មោះឱ្យត្រូវជាមួយ Telegram Webhook)
Route::post('/webhook', [TelegramWebhookController::class, 'handle']);

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
        // ពេលចុះឈ្មោះទូទៅ គឺឲ្យសិទ្ធិជា 'user' ធម្មតា
        'role' => 'user', 
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
        'role' => $user->role // បញ្ជូនសិទ្ធិប្រាប់ទៅកុំព្យូទ័រវិញផងដែរ
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
    // 📍 គ្រប់គ្រងអាសយដ្ឋានដឹកជញ្ជូន
    Route::get('/addresses', [\App\Http\Controllers\AddressController::class, 'index']);
    Route::post('/addresses', [\App\Http\Controllers\AddressController::class, 'store']);
    Route::delete('/addresses/{id}', [\App\Http\Controllers\AddressController::class, 'destroy']);
    
});

// ៥. ក្រុមទី ២៖ សម្រាប់តែម្ចាស់ហាងប៉ុណ្ណោះ (Admin Routes) 🛡️
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'index']);
    // គ្រប់គ្រង Categories
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store']);
    Route::delete('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'destroy']);
    
    // គ្រប់គ្រងទំនិញ (ទាល់តែមានកាត Admin ទើបចូលមកកន្លែងនេះបាន)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    // 📦 គ្រប់គ្រងវិក្កយបត្រអតិថិជន
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index']);
    Route::put('/orders/{id}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus']);
    // 📊 ទាញយករបាយការណ៍ Dashboard
    Route::get('/dashboard/analytics', [\App\Http\Controllers\DashboardController::class, 'getAnalytics']);
    // គ្រប់គ្រងគូប៉ុងបញ្ចុះតម្លៃ
    Route::get('/coupons', [\App\Http\Controllers\CouponController::class, 'index']);
    Route::post('/coupons', [\App\Http\Controllers\CouponController::class, 'store']);
    Route::delete('/coupons/{id}', [\App\Http\Controllers\CouponController::class, 'destroy']);
    // គ្រប់គ្រងជម្រើសទំនិញ (Variants)
    Route::post('/products/{product}/variants', [\App\Http\Controllers\ProductVariantController::class, 'store']);
    Route::delete('/variants/{id}', [\App\Http\Controllers\ProductVariantController::class, 'destroy']);
    
});