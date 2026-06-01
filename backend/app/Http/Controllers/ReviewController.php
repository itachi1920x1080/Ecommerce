<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // ១. ទាញយកមតិយោបល់ទាំងអស់របស់ទំនិញណាមួយ (Public)
    public function index($productId)
    {
        // ទាញយកមតិយោបល់ និងភ្ជាប់មកជាមួយនូវឈ្មោះអ្នកដែលបាន Comment (user)
        $reviews = Review::with('user:id,name') 
            ->where('product_id', $productId)
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    // ២. បញ្ចូលការវាយតម្លៃថ្មី (សម្រាប់តែ User ដែលបាន Login ប៉ុណ្ណោះ)
    public function store(Request $request, $productId)
    {
        // កំណត់លក្ខខណ្ឌ៖ ត្រូវតែមានពិន្ទុពី ១ ដល់ ៥
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        // ឆែកមើលថាតើទំនិញនោះពិតជាមានក្នុងប្រព័ន្ធមែនឬអត់
        $product = Product::findOrFail($productId);

        // បង្កើតការវាយតម្លៃថ្មីចូលក្នុង Database
        $review = Review::create([
            'user_id' => $request->user()->id, // ចាប់យក ID របស់អ្នកដែលកំពុង Login
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'អរគុណសម្រាប់ការវាយតម្លៃរបស់អ្នក!',
            'review' => $review
        ], 201);
    }
}