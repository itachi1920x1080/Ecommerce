<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    // មុខងារបញ្ជាទិញ និងគិតលុយ (Checkout)
    public function checkout(Request $request)
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            // ១. ទាញយកទំនិញពីកន្ត្រក
            $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'កន្ត្រករបស់អ្នកទទេស្អាត!'], 400);
            }

            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item->product->price * $item->quantity;
            }

            // ២. បង្កើត Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_method' => 'COD', // ឬយកចេញពី $request
                'status' => 'pending',
            ]);

            // ៣. បង្កើត Order Items និងកាត់ស្តុក
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            // ៤. លុបកន្ត្រក
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            // ៥. បាញ់សារចូល Telegram (បន្ទាប់ពី Commit ជោគជ័យ)
            $telegramToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if ($telegramToken && $chatId) {
                $message = "🛍️ <b>មានការបញ្ជាទិញថ្មី!</b>\n\n";
                $message .= "🧾 <b>លេខវិក្កយបត្រ:</b> #{$order->id}\n";
                $message .= "💰 <b>ទឹកប្រាក់សរុប:</b> $" . number_format($totalAmount, 2) . "\n";
                $message .= "💳 <b>វិធីបង់ប្រាក់:</b> COD\n\n";
                $message .= "👉 <i>សូមចូលទៅកាន់ Dashboard ដើម្បីរៀបចំឥវ៉ាន់!</i>";

                @Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);
            }

            return response()->json([
                'message' => 'ការបញ្ជាទិញជោគជ័យ!',
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'មានបញ្ហា!', 'error' => $e->getMessage()], 500);
        }
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