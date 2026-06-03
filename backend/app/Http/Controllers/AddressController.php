<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // ១. ទាញយកអាសយដ្ឋានទាំងអស់របស់ User ហ្នឹង
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderBy('is_default', 'desc')->get();
        return response()->json($addresses);
    }

    // ២. បន្ថែមអាសយដ្ឋានដឹកជញ្ជូនថ្មី
    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'full_address' => 'required|string',
            'city' => 'sometimes|string',
            'is_default' => 'boolean'
        ]);

        // ប្រសិនបើកំណត់ជា default, ត្រូវដក default ពីអាសយដ្ឋានចាស់ៗសិន
        if ($request->is_default) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($request->all());

        return response()->json([
            'message' => 'បានបន្ថែមអាសយដ្ឋានថ្មីជោគជ័យ!',
            'address' => $address
        ], 201);
    }

    // ៣. លុបអាសយដ្ឋាន
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json(['message' => 'អាសយដ្ឋានត្រូវបានលុប!']);
    }
}