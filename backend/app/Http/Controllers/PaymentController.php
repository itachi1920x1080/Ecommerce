<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function webhook(Request $request)
    {
        // ១. ទទួលទិន្នន័យពីធនាគារ
        $orderId = $request->input('tran_id'); // លេខកូដវិក្កយបត្រ
        $status = $request->input('status'); // ឧទាហរណ៍: SUCCESS

        // ២. (សំខាន់បំផុត) ផ្ទៀងផ្ទាត់សោរសុវត្ថិភាព (Signature) ដើម្បីការពារកុំឱ្យ Hacker បន្លំ
        // កូដត្រង់នេះអាស្រ័យលើធនាគារនីមួយៗ...

        // ៣. បើជោគជ័យ Update Database
        if ($status === 'SUCCESS') {
            $order = Order::find($orderId);
            if ($order && $order->status !== 'paid') {
                $order->status = 'paid';
                $order->save();

                // អាចបាញ់សារចូល Telegram ប្រាប់ Admin បន្ថែម
                try {
                    $botToken = env('TELEGRAM_BOT_TOKEN');
                    $chatId = env('TELEGRAM_CHAT_ID'); 
                    
                    $message = "💸 <b>ការបង់ប្រាក់ជោគជ័យ (KHQR)!</b>\n\n";
                    $message .= "🧾 <b>លេខវិក្កយបត្រ:</b> #{$order->id}\n";
                    $message .= "💰 <b>ទឹកប្រាក់សរុប:</b> $" . number_format($order->total_amount, 2) . "\n";
                    
                    \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'HTML',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Telegram Error: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }
}
