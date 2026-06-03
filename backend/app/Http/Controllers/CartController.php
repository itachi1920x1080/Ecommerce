<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    // ១. មើលទំនិញទាំងអស់ក្នុងកន្ត្រករបស់ខ្លួន (Method: GET)
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product') // ទាញយកព័ត៌មានលម្អិតរបស់ទំនិញ (ឈ្មោះ តម្លៃ រូបភាព) មកបង្ហាញជាមួយ
            ->get();

        return response()->json([
            'cart' => $cartItems
        ]);
    }

    // ២. បន្ថែមទំនិញចូលកន្ត្រក ឬបូកចំនួនថែម (Method: POST)
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id', // ត្រូវតែជា ID ទំនិញដែលមានពិតប្រាកដ
            'quantity' => 'required|integer|min:1',       // ចំនួនទិញយ៉ាងហោចណាស់ក៏ ១ ដែរ
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // ឆែកមើលថា តើ User ម្នាក់នេះបានដាក់ទំនិញមួយនេះចូលកន្ត្រកពីមុនមកហើយឬនៅ?
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // កាលបើមានទំនិញនេះក្នុងកន្ត្រកហើយ គឺគ្រាន់តែបូកបន្ថែមចំនួន (Quantity)
            $cartItem->quantity += $quantity;
            $cartItem->save();
            $message = 'បានបូកបន្ថែមចំនួនទំនិញក្នុងកន្ត្រកជោគជ័យ!';
        } else {
            // បើមិនទាន់មានទេ គឺបង្កើតជួរថ្មីមួយចូល Database
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            $message = 'បានបន្ថែមទំនិញចូលកន្ត្រកជោគជ័យ!';
        }

        return response()->json([
            'message' => $message,
            'data' => $cartItem->load('product') // បង្ហាញព័ត៌មានទំនិញមកជាមួយភ្លាមៗ
        ], 200);
    }

    // Update cart item quantity (Method: PUT)
    public function update($id, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'រកមិនឃើញទំនិញនេះក្នុងកន្ត្រកទេ'], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'បានកែប្រែចំនួនទំនិញជោគជ័យ!',
            'data' => $cartItem->load('product')
        ]);
    }

    // ៣. លុបទំនិញចេញពីកន្ត្រក (Method: DELETE)
    public function destroy($id, Request $request)
    {
        // ស្វែងរកទំនិញក្នុងកន្ត្រក ដែលជារបស់ User កំពុង Login
        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'រកមិនឃើញទំនិញនេះក្នុងកន្ត្រកទេ'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'បានលុបទំនិញចេញពីកន្ត្រកជោគជ័យ!']);
    }
}