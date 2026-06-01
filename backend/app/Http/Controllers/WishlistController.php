<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // ១. ទាញយកបញ្ជីទំនិញដែលបានចុចបេះដូងទាំងអស់របស់ User ម្នាក់នេះ
    public function index(Request $request)
    {
        // ទាញយក Wishlist ដោយភ្ជាប់មកជាមួយនូវព័ត៌មានទំនិញ (product) ព្រមទាំងឈ្មោះ category របស់ទំនិញនោះ
        $wishlists = Wishlist::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($wishlists);
    }

    // ២. មុខងារ Toggle៖ ចុចម្តង Add ចុចម្តងទៀត Remove
    public function toggle(Request $request, $productId)
    {
        $user = $request->user();

        // ឆែកមើលថាតើគាត់ធ្លាប់ចុចបេះដូងលើទំនិញនេះឬនៅ?
        $existingWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlist) {
            // បើមានហើយ -> លុបចេញវិញ (Remove)
            $existingWishlist->delete();
            return response()->json([
                'message' => 'បានដកចេញពីបញ្ជីចំណូលចិត្ត!',
                'status' => 'removed'
            ]);
        } else {
            // បើមិនទាន់មាន -> បន្ថែមចូល (Add)
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId
            ]);
            return response()->json([
                'message' => 'បានបន្ថែមចូលបញ្ជីចំណូលចិត្ត!',
                'status' => 'added'
            ], 201);
        }
    }
}