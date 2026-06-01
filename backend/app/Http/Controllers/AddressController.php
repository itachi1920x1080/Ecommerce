<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // ១. ទាញយកអាសយដ្ឋានទាំងអស់របស់ User ដែលបាន Login
    public function index(Request $request)
    {
        // ទាញយកដោយតម្រៀបអាសយដ្ឋាន Default មកមុនគេ
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();
        return response()->json($addresses);
    }

    // ២. បញ្ចូលអាសយដ្ឋានថ្មី
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100', // ឧ. ផ្ទះភ្នំពេញ
            'phone_number' => 'required|string|max:20',
            'address_line' => 'required|string',
            'city_province' => 'nullable|string',
            'is_default' => 'boolean'
        ]);

        // ប្រសិនបើគាត់កំណត់អាសយដ្ឋាននេះជា Default នោះត្រូវដក Default ពីអាសយដ្ឋានចាស់ៗរបស់គាត់សិន
        if ($request->is_default) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        // បង្កើតអាសយដ្ឋានថ្មី
        $address = $request->user()->addresses()->create([
            'title' => $request->title,
            'phone_number' => $request->phone_number,
            'address_line' => $request->address_line,
            'city_province' => $request->city_province,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'message' => 'បន្ថែមអាសយដ្ឋានជោគជ័យ!',
            'address' => $address
        ], 201);
    }

    // ៣. លុបអាសយដ្ឋាន
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();
        
        return response()->json(['message' => 'លុបអាសយដ្ឋានជោគជ័យ!']);
    }
}