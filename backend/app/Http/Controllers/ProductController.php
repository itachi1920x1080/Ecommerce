<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ១. ទាញយកទំនិញទាំងអស់ (មានមុខងារ Search, Filter, Pagination និង Category)
    // ១. ទាញយកទំនិញទាំងអស់ (មានមុខងារ Search, Filter, Pagination និង Category)
    public function index(Request $request)
    {
        // ភ្ជាប់ជាមួយ category និង variants ដើម្បីទាញយកមកព្រមគ្នា
        $query = Product::with(['category', 'variants']);

        // ក. មុខងារស្វែងរកតាមឈ្មោះ
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // ខ. មុខងារចម្រោះតាមតម្លៃទាបបំផុត
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // គ. មុខងារចម្រោះតាមតម្លៃខ្ពស់បំផុត
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // ឃ. បែងចែកទំព័រ (ប្រើកូដ paginate ដើមរបស់អ្នកវិញទើបត្រូវ)
        $products = $query->latest()->paginate(10);

        return response()->json($products);
    }   
    // ២. បញ្ចូលទំនិញថ្មី (សម្រាប់ Admin)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id', // ឆែកថាតើ category_id នេះពិតជាមានក្នុងប្រព័ន្ធឬអត់
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'message' => 'បញ្ចូលទំនិញជោគជ័យ!', 
            'product' => $product
        ], 201);
    }

    // ៣. មើលទំនិញតែមួយមុខលម្អិត
    // ៣. មើលទំនិញតែមួយមុខលម្អិត
    public function show($id)
    {
        // កន្លែងនេះទើបយើងប្រើ findOrFail($id) ដោយភ្ជាប់ category និង variants មកជាមួយ
        $product = Product::with(['category', 'variants'])->findOrFail($id);
        
        return response()->json($product);
    }

    // ៤. កែប្រែទិន្នន័យទំនិញ (សម្រាប់ Admin)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // បើមានរូបភាពថ្មី លុបរូបភាពចាស់ចោល រួចបញ្ចូលរូបភាពថ្មី
        if ($request->hasFile('image')) {
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }
            $product->image_url = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id ?? $product->category_id,
        ]);
        
        $product->save();

        return response()->json([
            'message' => 'កែប្រែទំនិញជោគជ័យ!', 
            'product' => $product
        ]);
    }

    // ៥. លុបទំនិញ (សម្រាប់ Admin)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // លុបរូបភាពចេញពី Folder ផងដែរ
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }
        
        $product->delete();

        return response()->json(['message' => 'លុបទំនិញជោគជ័យ!']);
    }
 }