<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category' => function ($query) {
            $query->select('id', 'name');
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create([
            'uuid' => generateUuid(16),
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'category_id' => $validatedData['category_id'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function show(Product $product)
    {
        $product->load('category:id,name');

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'category_id' => $validatedData['category_id'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
