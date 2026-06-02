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
        $user = $request->user();
        $carts = \App\Models\Cart::where('user_id', $user->id)->with('product')->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'កន្ត្រកទំនិញរបស់អ្នកទទេស្អាត!'], 400);
        }

        // ១. គណនាតម្លៃសរុប
        $totalAmount = 0;
        foreach ($carts as $cart) {
            $totalAmount += $cart->product->price * $cart->quantity;
        }

        // យក payment_method បើគ្មានទេ ដាក់ COD ជាលំនាំដើម
        $paymentMethod = $request->payment_method ?? 'COD';

        // ២. បង្កើតវិក្កយបត្រ (Order)
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);

        // ៣. បញ្ចូលទំនិញទៅក្នុងវិក្កយបត្រ (Order Items)
        foreach ($carts as $cart) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->product->price,
            ]);
        }

        // ៤. លុបទំនិញចេញពីកន្ត្រកវិញ
        \App\Models\Cart::where('user_id', $user->id)->delete();

        // ==========================================
        // 🟢 ៥. មុខងារថ្មី៖ បង្កើត KHQR ប្រសិនបើអតិថិជនរើសយក KHQR
        // ==========================================
        $qrCodeBase64 = null;
        if (strtoupper($paymentMethod) === 'KHQR') {
            // ទម្រង់ទិន្នន័យ KHQR (សន្មតសម្រាប់ Project របស់អ្នក)
            $merchantName = "RUPP Shop";
            $bakongAccount = "rupp_shop@aclb"; 
            
            // បង្កើតអត្ថបទសម្រាប់ស្កេន
            $khqrPayload = "KHQR://{$bakongAccount}?amount={$totalAmount}&currency=USD&merchant={$merchantName}&order_id={$order->id}";

            // បង្កើតរូបភាព QR Code ជាប្រភេទ Base64 (SVG)
            $qrImage = QrCode::format('svg')->size(300)->generate($khqrPayload);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrImage);
        }
        // ==========================================

        // ៦. មុខងារ Telegram (នៅរក្សាទុកដដែល)
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID'); 
            
            $message = "🛍 <b>មានការបញ្ជាទិញថ្មី!</b>\n\n";
            $message .= "🧾 <b>លេខវិក្កយបត្រ:</b> #{$order->id}\n";
            $message .= "💰 <b>ទឹកប្រាក់សរុប:</b> $" . number_format($totalAmount, 2) . "\n";
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

        // ៧. បញ្ជូនទិន្នន័យទៅ Frontend
        return response()->json([
            'message' => 'បញ្ជាទិញជោគជ័យ!',
            'order' => $order,
            'qr_code' => $qrCodeBase64 // បញ្ជូនកូដ QR ទៅឱ្យអតិថិជនមើល
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