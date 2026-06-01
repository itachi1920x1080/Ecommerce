<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 🛒 ១. មុខងារបញ្ជាទិញ និងគិតលុយ (Checkout - Pro Version)
    public function checkout(Request $request)
    {
        // តម្រូវឱ្យមានការជ្រើសរើសទីតាំងអាសយដ្ឋានមុនពេលទិញ
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'nullable|string',
        ]);

        $user = $request->user();
        
        // ទាញយកទំនិញទាំងអស់ពីកន្ត្រករបស់គាត់
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'កន្ត្រករបស់អ្នកទទេស្អាត! គ្មានអ្វីត្រូវគិតលុយទេ'], 400);
        }

        // ប្រើប្រាស់ DB Transaction ដើម្បីការពារសុវត្ថិភាព (បើ Error វានឹងអូសត្រលប់ក្រោយវិញទាំងអស់)
        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItemsData = [];

            // គណនាតម្លៃសរុប និងកាត់ស្តុកទំនិញ (គិតទាំង Product ធម្មតា និង Variant)
            foreach ($cartItems as $item) {
                $product = $item->product;
                $price = $product->price;

                // បើទំនិញក្នុងកន្ត្រកមានភ្ជាប់ជម្រើស (Variant)
                if ($item->product_variant_id) {
                    $variant = ProductVariant::findOrFail($item->product_variant_id);
                    if ($variant->stock < $item->quantity) {
                        throw new \Exception("ជម្រើសទំនិញ {$product->name} ({$variant->name}) មិនមានស្តុកគ្រប់គ្រាន់ទេ!");
                    }
                    $variant->decrement('stock', $item->quantity); // កាត់ស្តុក Variant
                    $price = $variant->price; // យកតម្លៃ Variant មកគិតលុយ
                } else {
                    // បើទំនិញធម្មតា អត់មានជម្រើស
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("ទំនិញ {$product->name} មិនមានស្តុកគ្រប់គ្រាន់ទេ!");
                    }
                    $product->decrement('stock', $item->quantity); // កាត់ស្តុក Product មេ
                }

                $totalAmount += ($price * $item->quantity);

                // រៀបចំទិន្នន័យសម្រាប់ Insert ចូល OrderItem
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $item->product_variant_id ?? null,
                    'quantity' => $item->quantity,
                    'price' => $price,
                ];
            }

            // បង្កើតវិក្កយបត្រមេ (Order)
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method ?? 'COD',
                'status' => 'pending',
            ]);

            // បញ្ចូលទំនិញលម្អិតទៅក្នុង Order Items
            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }

            // លុបទំនិញទាំងអស់ចេញពីកន្ត្រក (Clear Cart) បន្ទាប់ពីទិញរួចរាល់
            Cart::where('user_id', $user->id)->delete();

            DB::commit(); // អនុម័តរក្សាទុកទិន្នន័យចូល Database

            return response()->json([
                'message' => 'ការបញ្ជាទិញទទួលបានជោគជ័យ!',
                'order_id' => $order->id,
                'total_paid' => $totalAmount
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // បោះបង់ប្រតិបត្តិការទាំងអស់ ប្រសិនបើមានបញ្ហាអស់ស្តុក
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // 📦 ២. សម្រាប់ User: មើលប្រវត្តិការទិញរបស់ខ្លួនឯង
    public function myOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'items.variant']) // ទាញយកទាំងឈ្មោះ Product និងឈ្មោះ Variant
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // 📊 ៣. សម្រាប់ Admin: ទាញយកវិក្កយបត្រទាំងអស់
    public function index()
    {
        $orders = Order::with(['user', 'items.product', 'items.variant'])->latest()->paginate(10);
        return response()->json($orders);
    }

    // 🔄 ៤. សម្រាប់ Admin: ប្តូរស្ថានភាពវិក្កយបត្រ (Pending -> Shipped -> Delivered)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => "ស្ថានភាពវិក្កយបត្រត្រូវបានប្តូរទៅជា {$request->status} ជោគជ័យ!",
            'order' => $order
        ]);
    }
}