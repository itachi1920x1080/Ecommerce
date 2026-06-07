<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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

        // ឃ. មុខងារចម្រោះតាម Category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // ង. មុខងារចម្រោះតាម Sale (discount_percent > 0)
        if ($request->has('sale') && $request->sale === 'true') {
            $query->where('discount_percent', '>', 0);
        }

        // ច. បិទមិនបង្ហាញផលិតផលដែល admin កំណត់ថា hide តែប៉ុណ្ណោះ
        // ✅ FIX: include NULL rows too — whereNull handles products with no status set
        $query->where(function($q) {
            $q->where('out_of_stock_status', '!=', 'hide')
              ->orWhereNull('out_of_stock_status');
        });

        // ឆ. មុខងារតម្រៀប
        if ($request->has('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        // ជ. បែងចែកទំព័រ — ✅ FIX: cast to int និង whitelist តម្លៃដែលអនុញ្ញាត
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100, 300]) ? $perPage : 20;
        $products = $query->paginate($perPage);

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
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'out_of_stock_status' => 'nullable|string|in:show,hide,preorder',
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
            'discount_percent' => $request->discount_percent ?? 0,
            'out_of_stock_status' => $request->out_of_stock_status ?? 'show',
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'message' => 'បញ្ចូលទំនិញជោគជ័យ!',
            'product' => $product
        ], 201);
    }

    // ៣. មើលទំនិញតែមួយមុខលម្អិត
    public function show($id)
    {
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
            'out_of_stock_status' => 'nullable|string|in:show,hide,preorder',
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
            'discount_percent' => $request->discount_percent ?? $product->discount_percent,
            'out_of_stock_status' => $request->out_of_stock_status ?? 'show',
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