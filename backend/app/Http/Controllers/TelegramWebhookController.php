<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product; // កុំភ្លេច Import Model នេះ!
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $update = $request->all();
            
            // បញ្ឈប់ដំណើរការបើគ្មានសារជាអក្សរ
            if (!isset($update['message']['text'])) {
                return response()->json(['status' => 'ok']);
            }

            $text = $update['message']['text'];
            $chatId = $update['message']['chat']['id'];
            
            // ១. ដាក់ Token ផ្ទាល់សិន ដើម្បីប្រាកដថាវាដើរ (អាចប្តូរទៅ env វិញនៅពេលក្រោយ)
            $botToken = "8895814464:AAFS4-02QXSMKgy4nuuxs-thJL4UYpVRmkg";
            $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

            // ----------------------------------------------------
            // មុខងារទី ១: /start ឬ /help
            // ----------------------------------------------------
            if (str_starts_with($text, '/start') || str_starts_with($text, '/help')) {
                $message = "សួស្តី! ស្វាគមន៍មកកាន់ RUPP Shop 🛍\n\n";
                $message .= "បញ្ជាដែលអ្នកអាចប្រើបាន:\n";
                $message .= "🛒 /products - មើលទំនិញថ្មីៗ\n";
                $message .= "📦 /track [លេខវិក្កយបត្រ] - ឆែកស្ថានភាពទំនិញ\n";
                $message .= "📞 /contact - ព័ត៌មានទំនាក់ទំនង\n";

                // ⚠️ ប្តូរលីង localhost ទៅជាលីង Ngrok
                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🌐 ចូលទិញទំនិញក្នុង Web', 'url' => 'https://tapered-motocross-versus.ngrok-free.dev']],
                        [['text' => '💬 ឆាតសួរព័ត៌មាន Admin', 'url' => 'https://t.me/mk_bo1879']]
                    ]
                ];

                Http::post($apiUrl, [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'reply_markup' => json_encode($keyboard)
                ]);
            }

            // ----------------------------------------------------
            // មុខងារទី ២: /contact
            // ----------------------------------------------------
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

            // ----------------------------------------------------
            // មុខងារទី ៣: /track
            // ----------------------------------------------------
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

            // ----------------------------------------------------
            // មុខងារទី ៤: /products
            // ----------------------------------------------------
            elseif (str_starts_with($text, '/products')) {
                // ទាញយកទំនិញ ៥ ចុងក្រោយពី Database
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

                // ⚠️ ប្តូរលីង localhost ទៅជាលីង Ngrok
                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🛒 មើលទំនិញទាំងអស់ក្នុង Web', 'url' => 'https://tapered-motocross-versus.ngrok-free.dev/api/products']]
                    ]
                ];

                Http::post($apiUrl, [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($keyboard)
                ]);
            }

            // ----------------------------------------------------
            // មុខងារទី ៥: ករណីវាយខុស
            // ----------------------------------------------------
            else {
                Http::post($apiUrl, [
                    'chat_id' => $chatId,
                    'text' => "សុំទោស ខ្ញុំមិនយល់ទេ។ សូមវាយ /help ដើម្បីមើលបញ្ជា។"
                ]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Telegram Bot Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}