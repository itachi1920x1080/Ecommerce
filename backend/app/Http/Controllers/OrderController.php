<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    // មុខងារបញ្ជាទិញ និងគិតលុយ (Checkout)
    public function checkout(Request $request)
    {
        $user = $request->user();
        
        // ១. ទាញយកទំនិញទាំងអស់ពីកន្ត្រករបស់គាត់
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

        // បើគ្មានទំនិញក្នុងកន្ត្រកទេ បញ្ឈប់ការគិតលុយ
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'កន្ត្រករបស់អ្នកទទេស្អាត! គ្មានអ្វីត្រូវគិតលុយទេ'], 400);
        }

        $totalAmount = 0;

        // ២. គណនាតម្លៃសរុប (យកតម្លៃទំនិញ x ចំនួនដែលទិញ)
        foreach ($cartItems as $item) {
            $totalAmount += $item->product->price * $item->quantity;
        }

        // ៣. បង្កើតវិក្កយបត្រថ្មី (Order)
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'status' => 'pending', // ស្ថានភាព៖ រង់ចាំការទូទាត់ប្រាក់
        ]);

        // ៤. ផ្ទេរទំនិញពីកន្ត្រក ចូលវិក្កយបត្រ & កាត់ស្តុកទំនិញ
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price, 
            ]);

            // កាត់ស្តុកទំនិញចេញពីឃ្លាំង (ស្តុកដើម ដក ចំនួនទិញ)
            $item->product->decrement('stock', $item->quantity);
        }

        // ៥. លុបទំនិញទាំងអស់ចេញពីកន្ត្រក (Clear Cart)
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'ការបញ្ជាទិញទទួលបានជោគជ័យ!',
            'order' => $order->load('items.product') // បង្ហាញវិក្កយបត្រ និងទំនិញមកវិញ
        ], 201);
    }
    // ១. សម្រាប់ User: មើលប្រវត្តិការទិញរបស់ខ្លួនឯង (My Orders)
    public function myOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product') // ទាញទំនិញដែលបានទិញមកជាមួយ
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // ២. សម្រាប់ Admin: ទាញយកវិក្កយបត្រទាំងអស់
    public function index()
    {
        // ទាញយក Orders ទាំងអស់ ព្រមទាំងឈ្មោះអ្នកទិញ (user) និងទំនិញ (items)
        $orders = Order::with(['user', 'items.product'])->latest()->paginate(10);
        return response()->json($orders);
    }

    // ៣. សម្រាប់ Admin: ប្តូរស្ថានភាពវិក្កយបត្រ (ឧ. ពី pending ទៅ completed)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'ស្ថានភាពវិក្កយបត្រត្រូវបានផ្លាស់ប្តូរជោគជ័យ!',
            'order' => $order
        ]);
    }
}