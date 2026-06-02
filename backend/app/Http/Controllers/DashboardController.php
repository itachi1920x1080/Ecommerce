<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // ទាញយកទិន្នន័យសង្ខេបសម្រាប់ផ្ទាំង Dashboard
    public function getAnalytics()
    {
        // ១. គណនាប្រាក់ចំណូលសរុប (យកតែវិក្កយបត្រដែល completed)
        // កែមកបែបនេះវិញ៖
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');   

        // ២. រាប់ចំនួនការបញ្ជាទិញសរុបទាំងអស់
        $totalOrders = Order::count();

        // ៣. រាប់ចំនួនអតិថិជន (User ធម្មតា)
        $totalCustomers = User::where('role', 'user')->count();

        // ៤. រាប់ចំនួនទំនិញដែលមានក្នុងហាង
        $totalProducts = Product::count();

        // ៥. ទាញយកវិក្កយបត្រថ្មីៗ ៥ ចុងក្រោយ ជាមួយឈ្មោះអ្នកទិញ
        $recentOrders = Order::with('user:id,name,email')
            ->latest()
            ->take(5)
            ->get();

        // ៦. បញ្ជូនទិន្នន័យទាំងអស់ទៅកាន់ Frontend ជាទម្រង់ JSON
        return response()->json([
            'status' => 'success',
            'analytics' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
            ],
            'recent_orders' => $recentOrders
        ]);
    }
}