<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $update = $request->all();
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

            // ==========================================
            // ផ្នែកទី ១៖ ចាប់យកការចុចប៊ូតុង (Callback Query)
            // ==========================================
            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $callbackData = $callbackQuery['data']; 
                $chatId = $callbackQuery['message']['chat']['id'];
                $messageId = $callbackQuery['message']['message_id'];
                $originalText = $callbackQuery['message']['text'];

                // 🟢 ប្រាប់ Telegram ថាទទួលបានការចុចហើយ (បញ្ឈប់ការវិលៗលើប៊ូតុង)
                Http::post("https://api.telegram.org/bot{$botToken}/answerCallbackQuery", [
                    'callback_query_id' => $callbackQuery['id']
                ]);

                // បំបែកយកពាក្យបញ្ជា និងលេខ ID
                $parts = explode('_', $callbackData);
                $action = $parts[0];
                $orderId = $parts[1] ?? null;

                if ($orderId) {
                    $order = Order::with(['user', 'address'])->find($orderId);
                    if ($order) {
                        // កែប្រែស្ថានភាព
                        $statusText = '';
                        $deliveryInfo = '';
                        
                        if ($action === 'approve') {
                            // 🟢 ប្តូរពី 'completed' ទៅ 'finding_driver' ដើម្បីឱ្យ Driver អាចឃើញ
                            $order->update(['status' => 'finding_driver']);
                            $statusText = "✅ បានយល់ព្រម (កំពុងស្វែងរកអ្នកដឹកជញ្ជូន...)";

                            // បន្ថែមព័ត៌មានដឹកជញ្ជូន
                            if ($order->address) {
                                $deliveryInfo .= "\n\n📦 <b>ព័ត៌មានអ្នកទទួល (Delivery Info):</b>\n";
                                $deliveryInfo .= "👤 ឈ្មោះ: {$order->address->receiver_name}\n";
                                $deliveryInfo .= "📞 លេខទូរស័ព្ទ: {$order->address->phone_number}\n";
                                $deliveryInfo .= "🏠 ទីតាំង: {$order->address->full_address}, {$order->address->city}";
                            }
                        } elseif ($action === 'reject') {
                            $order->update(['status' => 'cancelled']);
                            $statusText = "❌ បានបដិសេធ (Cancelled)";
                        }

                        // កែប្រែសារចាស់ ដើម្បីលុបប៊ូតុងចោល និងបង្ហាញស្ថានភាពថ្មី
                        $newText = $originalText . "\n\n👉 ស្ថានភាពបច្ចុប្បន្ន: " . $statusText . $deliveryInfo;
                        Http::post("https://api.telegram.org/bot{$botToken}/editMessageText", [
                            'chat_id' => $chatId,
                            'message_id' => $messageId,
                            'text' => $newText,
                            'parse_mode' => 'HTML'
                        ]);
                    }
                }
                return response()->json(['status' => 'success']);
            }

            // ==========================================
            // ផ្នែកទី ២៖ ចាប់យកការវាយបញ្ជាអក្សរ (Text Commands)
            // ==========================================
            if (isset($update['message']['text'])) {
                $text = $update['message']['text'];
                $chatId = $update['message']['chat']['id'];

                if (str_starts_with($text, '/start') || str_starts_with($text, '/help')) {
                    $message = "សួស្តី! ស្វាគមន៍មកកាន់ RUPP Shop 🛍\n\n";
                    $message .= "បញ្ជាដែលអ្នកអាចប្រើបាន:\n";
                    $message .= "🛒 /products - មើលទំនិញថ្មីៗ\n";
                    $message .= "📦 /track [លេខវិក្កយបត្រ] - ឆែកស្ថានភាពទំនិញ\n";
                    $message .= "📞 /contact - ព័ត៌មានទំនាក់ទំនង\n";

                    $keyboard = [
                        'inline_keyboard' => [
                            [['text' => '🌐 ចូលទិញទំនិញក្នុង Web', 'url' => 'https://beautiful-reflection-production.up.railway.app']],
                            [['text' => '💬 ឆាតសួរព័ត៌មាន Admin', 'url' => 'https://t.me/mk_bo1879']]
                        ]
                    ];

                    Http::post($apiUrl, [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'reply_markup' => json_encode($keyboard)
                    ]);
                }
                elseif (str_starts_with($text, '/contact')) {
                    $message = "📍 **ទីតាំងហាង:** សាកលវិទ្យាល័យភូមិន្ទភ្នំពេញ (RUPP)\n";
                    $message .= "📞 **លេខទូរស័ព្ទ:** 012 345 678\n";
                    $message .= "✉️ **អ៊ីមែល:** support@ruppshop.com";

                    Http::post($apiUrl, [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown'
                    ]);
                }
                elseif (str_starts_with($text, '/track')) {
                    $parts = explode(' ', $text);
                    $orderId = $parts[1] ?? null;

                    if ($orderId) {
                        $order = Order::find($orderId);
                        $message = $order ? "🧾 វិក្កយបត្រ #{$orderId}\nស្ថានភាព: " . strtoupper($order->status) 
                                          : "❌ មិនឃើញមានវិក្កយបត្រលេខ #{$orderId} ទេ។";
                    } else {
                        $message = "⚠️ សូមវាយបញ្ជាឱ្យបានត្រឹមត្រូវ។ \n👉 ឧទាហរណ៍: /track 5";
                    }

                    Http::post($apiUrl, ['chat_id' => $chatId, 'text' => $message]);
                }
                elseif (str_starts_with($text, '/products')) {
                    $products = Product::latest()->take(5)->get();

                    if ($products->isEmpty()) {
                        $message = "សុំទោស! មិនទាន់មានទំនិញថ្មីៗនៅក្នុងស្តុកទេពេលនេះ 😥";
                    } else {
                        $message = "🔥 <b>ទំនិញថ្មីៗទើបមកដល់ (New Arrivals):</b>\n\n";
                        foreach ($products as $index => $pro) {
                            $message .= "🔹 " . ($index + 1) . ". " . $pro->name . " - <b>$" . number_format($pro->price, 2) . "</b>\n";
                        }
                        $message .= "\n👉 សូមចុចប៊ូតុងខាងក្រោមដើម្បីទិញ ឬមើលបន្ថែម!";
                    }

                    $keyboard = [
                        'inline_keyboard' => [
                            [['text' => '🛒 មើលទំនិញទាំងអស់ក្នុង Web', 'url' => 'https://beautiful-reflection-production.up.railway.app/products']]
                        ]
                    ];

                    Http::post($apiUrl, [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode($keyboard)
                    ]);
                }
                else {
                    Http::post($apiUrl, [
                        'chat_id' => $chatId,
                        'text' => "សុំទោស ខ្ញុំមិនយល់ទេ។ សូមវាយ /help ដើម្បីមើលបញ្ជា។"
                    ]);
                }

                return response()->json(['status' => 'success']);
            }

            // បើមិនមែនការចុចប៊ូតុង ហើយក៏មិនមែនសារអក្សរ
            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Telegram Bot Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}