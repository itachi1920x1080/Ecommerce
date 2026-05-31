<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ១. ទាញយកទំនិញទាំងអស់ (Method: GET) សម្រាប់ឲ្យ Frontend បង្ហាញលើទំព័រ Shop
   // ១. ទាញយកទំនិញទាំងអស់ (មានមុខងារ Search, Filter និង Pagination)
    public function index(Request $request)
    {
        // ចាប់ផ្តើមរៀបចំការទាញទិន្នន័យពីតារាង products
        $query = Product::query();

        // ក. មុខងារស្វែងរកតាមឈ្មោះ (Search)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        // ខ. មុខងារចម្រោះតាមតម្លៃទាបបំផុត (Min Price)
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // គ. មុខងារចម្រោះតាមតម្លៃខ្ពស់បំផុត (Max Price)
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // ឃ. បែងចែកទំព័រ (បង្ហាញត្រឹម ១០ ទំនិញក្នុង ១ ទំព័រ)
        // ប្រើលេខរៀងថ្មីៗមុនគេ (latest)
        $products = $query->latest()->paginate(10);

        return response()->json($products);
    }
    // ២. បញ្ចូលទំនិញថ្មី (Method: POST)
    // ២. បញ្ចូលទំនិញថ្មី និង Upload រូបភាព (Method: POST)
    public function store(Request $request)
    {
        // ត្រួតពិនិត្យថាទិន្នន័យ និង File រូបភាព ត្រឹមត្រូវឬអត់
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            // ដូរពី string ទៅជាការឆែកមើល File រូបភាពវិញ
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $imagePath = null;

        // បើមានគេភ្ជាប់រូបភាពមក ឲ្យ Save វាចូលក្នុង Folder public/products
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // បញ្ចូលទៅក្នុង Database (យកទីតាំងរូបភាព $imagePath ទៅរក្សាទុក)
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'message' => 'ទំនិញនិងរូបភាពត្រូវបានបញ្ចូលជោគជ័យ!',
            'data' => $product
        ], 201);
    }

    // ៣. មើលទំនិញតែមួយ (Method: GET) សម្រាប់ពេលគេចុចមើល Detail ទំនិញ
    public function show($id)
    {
        // បើកកូដ fail ពេលរកមិនឃើញ វានឹងបោះ Error 404 ទៅកាន់ Frontend ដោយស្វ័យប្រវត្តិ
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // ៤. កែប្រែព័ត៌មានទំនិញ (Method: PUT/PATCH)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // កែប្រែទិន្នន័យដែលគេបញ្ជូនមកថ្មី
        $product->update($request->all());

        return response()->json([
            'message' => 'ទំនិញត្រូវបានកែប្រែជោគជ័យ!',
            'data' => $product
        ]);
    }

    // ៥. លុបទំនិញ (Method: DELETE)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'ទំនិញត្រូវបានលុបចោលដោយជោគជ័យ!'
        ]);
    }
}