<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // ១. ទាញយកប្រភេទ (Categories) ទាំងអស់
    public function index()
    {
        // ទាញយក Categories ព្រមទាំងរាប់ចំនួនទំនិញដែលមានក្នុង Category នីមួយៗ
        $categories = Category::withCount('products')->get();
        return response()->json($categories);
    }

    // ២. បង្កើតប្រភេទថ្មី (សម្រាប់ Admin)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name), // បំប្លែងឈ្មោះទៅជាទម្រង់លីង ឧ. "My PC" -> "my-pc"
        ]);

        return response()->json([
            'message' => 'បានបង្កើតប្រភេទថ្មីដោយជោគជ័យ!',
            'category' => $category
        ], 201);
    }

    // ៣. លុបប្រភេទចោល (សម្រាប់ Admin)
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'ប្រភេទត្រូវបានលុបចេញពីប្រព័ន្ធ!'
        ]);
    }
}