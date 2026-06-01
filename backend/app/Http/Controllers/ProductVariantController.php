<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    // ១. មើលជម្រើសទាំងអស់របស់ទំនិញណាមួយ (សម្រាប់ Public)
    public function index($productId)
    {
        $variants = ProductVariant::where('product_id', $productId)->get();
        return response()->json($variants);
    }

    // ២. បន្ថែមជម្រើសថ្មីទៅឱ្យទំនិញ (សម្រាប់តែ Admin)
    public function store(Request $request, $productId)
    {
        $request->validate([
            'name' => 'required|string|max:255', // ឧ. "ពណ៌ក្រហម", "256GB"
            'price' => 'required|numeric|min:0', // តម្លៃសម្រាប់ជម្រើសនេះ
            'stock' => 'required|integer|min:0', // ចំនួនស្តុករបស់វា
        ]);

        // ឆែករកមើលថាតើទំនិញនោះពិតជាមានមែនឬទេ
        $product = Product::findOrFail($productId);

        // បង្កើតជម្រើសថ្មីភ្ជាប់ទៅនឹងទំនិញនោះ
        $variant = $product->variants()->create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json([
            'message' => 'បន្ថែមជម្រើសទំនិញជោគជ័យ!',
            'variant' => $variant
        ], 201);
    }

    // ៣. លុបជម្រើសទំនិញ (សម្រាប់តែ Admin)
    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        
        return response()->json(['message' => 'លុបជម្រើសទំនិញជោគជ័យ!']);
    }
}