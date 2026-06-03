<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\TelegramService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    // មុខងារបញ្ជាទិញ និងគិតលុយ (Checkout)
    public function checkout(Request $request)
    {
        // ១. ត្រួតពិនិត្យថាមានបោះ ID ទីតាំងមកឬអត់
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'sometimes|string'
        ]);

        $user = $request->user();

        // ២. ទាញយកទំនិញទាំងអស់ពីកន្ត្រក (Cart) របស់ User ហ្នឹង
        // យើងនឹងរក្សាឈ្មោះ methods ដែលអ្នកធ្លាប់មាន ដូចជា carts()->with('product')->get();
        // បើតាមកូដចាស់គឺ ប្រើ \App\Models\Cart::where('user_id', $user->id)->with('product')->get(); 
        $cartItems = \App\Models\Cart::where('user_id', $user->id)->with('product')->get(); 

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'កន្ត្រករបស់អ្នកទទេស្អាត! មិនមានទំនិញសម្រាប់ទូទាត់ទេ។'], 400);
        }

        // ៣. ប្រើ DB Transaction ដើម្បីការពារដាច់អុីនធឺណិតពាក់កណ្តាលទី ធ្វើឱ្យបាត់លុយ
        DB::beginTransaction();

        try {
            $totalPrice = 0;

            // គណនាតម្លៃសរុបជាមុនសិន
            foreach ($cartItems as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            // ៤. បង្កើតវិក្កយបត្រ (Order) ថ្មី
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->payment_method ?? 'cod',
            ]);

            // ៥. ផ្ទេរទំនិញពី Cart ចូលទៅកាន់ Order Items
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price, // រក្សាទុកតម្លៃដើមនៅពេលទិញ
                ]);
            }

            // ៦. លុបទំនិញក្នុងកន្ត្រកចោល ព្រោះទិញរួចហើយ
            \App\Models\Cart::where('user_id', $user->id)->delete(); 

            // ==========================================
            // 🟢 បង្កើត KHQR ប្រសិនបើអតិថិជនរើសយក KHQR
            // ==========================================
            $qrCodeBase64 = null;
            $paymentMethod = $request->payment_method ?? 'cod';
            if (strtoupper($paymentMethod) === 'KHQR') {
                $merchantName = "RUPP Shop";
                $bakongAccount = "rupp_shop@aclb"; 
                $khqrPayload = "KHQR://{$bakongAccount}?amount={$totalPrice}&currency=USD&merchant={$merchantName}&order_id={$order->id}";
                $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($khqrPayload);
                $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrImage);
            }

            // ==========================================
            // 🟢 មុខងារ Telegram 
            // ==========================================
            try {
                $botToken = env('TELEGRAM_BOT_TOKEN');
                $chatId = env('TELEGRAM_CHAT_ID'); 
                
                $message = "🛍 <b>មានការបញ្ជាទិញថ្មី!</b>\n\n";
                $message .= "🧾 <b>លេខវិក្កយបត្រ:</b> #{$order->id}\n";
                $message .= "💰 <b>ទឹកប្រាក់សរុប:</b> $" . number_format($totalPrice, 2) . "\n";
                $message .= "💳 <b>វិធីបង់ប្រាក់:</b> {$paymentMethod}\n\n";
                $message .= "👉 <i>សូមជ្រើសរើសសកម្មភាពខាងក្រោម៖</i>";

                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ យល់ព្រម', 'callback_data' => "approve_{$order->id}"],
                            ['text' => '❌ បដិសេធ', 'callback_data' => "reject_{$order->id}"]
                        ]
                    ]
                ];

                \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($keyboard)
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Telegram Error: ' . $e->getMessage());
            }

            DB::commit(); // រក្សាទុកចូល Database ផ្លូវការ

            return response()->json([
                'message' => 'ការបញ្ជាទិញទទួលបានជោគជ័យ!',
                'order' => $order,
                'qr_code' => $qrCodeBase64 // បញ្ជូនកូដ QR ទៅឱ្យអតិថិជនមើល
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // បើមាន Error បង្វិលទិន្នន័យថយក្រោយវិញទាំងអស់
            return response()->json(['message' => 'មានបញ្ហាក្នុងការបញ្ជាទិញ សូមព្យាយាមម្តងទៀត!'], 500);
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
            'status' => 'required|in:pending,processing,paid,completed,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'ស្ថានភាពវិក្កយបត្រត្រូវបានផ្លាស់ប្តូរជោគជ័យ!',
            'order' => $order
        ]);
    }

    // ៤. សម្រាប់ Frontend ឆែកមើលថាបង់លុយរួចឬនៅ
    public function checkStatus($id)
    {
        $order = Order::findOrFail($id);
        return response()->json(['status' => $order->status]);
    }
}