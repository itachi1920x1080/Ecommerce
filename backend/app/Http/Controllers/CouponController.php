<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Carbon\Carbon; // សម្រាប់គណនាថ្ងៃខែឆ្នាំ

class CouponController extends Controller
{
    // ១. ទាញយកបញ្ជី Coupon ទាំងអស់ (សម្រាប់ Admin)
    public function index()
    {
        return response()->json(Coupon::latest()->get());
    }

    // ២. បង្កើត Coupon ថ្មី (សម្រាប់ Admin)
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($request->code), // បំប្លែងទៅអក្សរធំទាំងអស់ដោយស្វ័យប្រវត្តិ
            'type' => $request->type,
            'value' => $request->value,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return response()->json(['message' => 'បង្កើតគូប៉ុងជោគជ័យ!', 'coupon' => $coupon], 201);
    }

    // ៣. ផ្ទៀងផ្ទាត់គូប៉ុង ពេលអតិថិជនយកមកប្រើនៅកន្លែង Checkout (សម្រាប់ Public/User)
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', $request->code)->first();

        // លក្ខខណ្ឌទី១៖ ឆែកមើលថាតើមានកូដនេះក្នុងប្រព័ន្ធអត់?
        if (!$coupon) {
            return response()->json(['message' => 'កូដបញ្ចុះតម្លៃមិនត្រឹមត្រូវទេ!'], 404);
        }

        // លក្ខខណ្ឌទី២៖ ឆែកមើលថាតើកូដនេះត្រូវបានបិទ (is_active = false) ឬនៅ?
        if (!$coupon->is_active) {
            return response()->json(['message' => 'កូដបញ្ចុះតម្លៃនេះត្រូវបានផ្អាកការប្រើប្រាស់ហើយ!'], 400);
        }

        // លក្ខខណ្ឌទី៣៖ ឆែកមើលថ្ងៃផុតកំណត់
        if ($coupon->expires_at && Carbon::now()->greaterThan($coupon->expires_at)) {
            return response()->json(['message' => 'កូដបញ្ចុះតម្លៃនេះបានផុតកំណត់ហើយ!'], 400);
        }

        // បើឆ្លងកាត់លក្ខខណ្ឌខាងលើអស់ហើយ គឺអាចប្រើបាន
        return response()->json([
            'message' => 'កូដបញ្ចុះតម្លៃអាចប្រើប្រាស់បាន!',
            'coupon' => $coupon
        ]);
    }

    // ៤. លុបគូប៉ុងចោល (សម្រាប់ Admin)
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(['message' => 'លុបគូប៉ុងជោគជ័យ!']);
    }
}