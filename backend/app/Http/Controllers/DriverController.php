<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class DriverController extends Controller
{
    // 1. Get available orders (Orders with status 'finding_driver' and no driver assigned)
    public function getAvailableOrders(Request $request)
    {
        $orders = Order::with(['user', 'address', 'items.product'])
            ->where('status', 'finding_driver')
            ->whereNull('driver_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // 1.5 Get driver's active deliveries
    public function getActiveDeliveries(Request $request)
    {
        $orders = Order::with(['user', 'address', 'items.product'])
            ->where('driver_id', $request->user()->id)
            ->whereIn('status', ['driver_assigned', 'at_restaurant', 'delivering'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // 2. Accept an order
    public function acceptOrder(Request $request, $id)
    {
        $order = Order::with('user')->find($id);

        if (!$order) {
            return response()->json(['message' => 'រកមិនឃើញវិក្កយបត្រនេះទេ (Order not found)'], 404);
        }

        if ($order->driver_id !== null || $order->status !== 'finding_driver') {
            return response()->json(['message' => 'វិក្កយបត្រនេះមានអ្នកទទួលយកបាត់ហើយ (Already taken)'], 400);
        }

        $order->update([
            'driver_id' => $request->user()->id,
            'status' => 'driver_assigned'
        ]);

        // ផ្ញើសារទៅ Telegram Admin ថា Driver បានទទួលយកការដឹកជញ្ជូន
        $this->sendTelegramNotification("🛵 *ដំណឹងអ្នកដឹកជញ្ជូន*\nអ្នកដឹកជញ្ជូន ទទួលបានការងារនេះហើយ!\n- អ្នកដឹក: {$request->user()->name}\n- លេខវិក្កយបត្រ: #{$order->id}\n- អតិថិជន: {$order->user->name}");

        return response()->json([
            'message' => 'ទទួលយកការដឹកជញ្ជូនជោគជ័យ! (Order accepted successfully)',
            'order' => $order
        ]);
    }

    // 3. Update order status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:at_restaurant,delivering,delivered',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120' // up to 5MB
        ]);

        $order = Order::with('user')->find($id);

        if (!$order) {
            return response()->json(['message' => 'រកមិនឃើញវិក្កយបត្រនេះទេ (Order not found)'], 404);
        }

        if ($order->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'អ្នកមិនមែនជាអ្នកដឹកជញ្ជូនសម្រាប់វិក្កយបត្រនេះទេ (Unauthorized)'], 403);
        }

        $updateData = ['status' => $request->status];

        if ($request->hasFile('image') && $request->status === 'delivered') {
            $imagePath = $request->file('image')->store('deliveries', 'public');
            $updateData['delivery_photo_url'] = $imagePath;
        }

        $order->update($updateData);

        // ផ្ញើសារទៅ Telegram ពេលស្ថានភាពប្រែប្រួល
        $statusEmoji = $request->status === 'delivered' ? '✅' : '🛵';
        $statusKhmer = [
            'at_restaurant' => 'បានមកដល់ហាងហើយ',
            'delivering'    => 'កំពុងដឹកជញ្ជូនទៅកាន់អតិថិជន',
            'delivered'     => 'បានប្រគល់ទំនិញដល់អតិថិជនដោយជោគជ័យ',
        ];
        $stTxt = $statusKhmer[$request->status] ?? $request->status;

        $this->sendTelegramNotification("{$statusEmoji} *បច្ចុប្បន្នភាពការដឹកជញ្ជូន (Order #{$order->id})*\n- ស្ថានភាព: {$stTxt}\n- អ្នកដឹក: {$request->user()->name}\n- អតិថិជន: {$order->user->name}");

        return response()->json([
            'message' => "ស្ថានភាពត្រូវបានផ្លាស់ប្តូរទៅជា {$request->status} ជោគជ័យ!",
            'order' => $order
        ]);
    }

    // Function ជំនួយសម្រាប់ផ្ញើសារទៅ Telegram
    private function sendTelegramNotification($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($botToken && $chatId) {
            \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        }
    }
}
