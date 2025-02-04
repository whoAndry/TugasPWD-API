<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
            
    public function index(Request $request)
    {
        $query = $request->query('name');

        if ($query) {
            $products = Product::where('name', 'like', "%{$query}%")->get();
        } else {
            $products = Product::all();
        }

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([ 
                'name' => 'required|string|max:255', 
                'price' => 'required|numeric|min:0', 
                'stock' => 'required|integer|min:0' 
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 400);
        }

        $product = Product::create($request->all()); 
        return response()->json([
            'success' => true,
            'data' => $product 
        ], 201); 
    }

   
    public function show(string $id)
    {
        $product = Product::find($id);      
        if (!$product) {         
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);     
        }      
        return response()->json([
            'success' => true,
            'data' => $product     
        ], 200); 
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);      
        if (!$product) {         
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);     
        }      

        try {
            $request->validate([         
                'name' => 'sometimes|required|string|max:255',         
                'price' => 'sometimes|required|numeric|min:0',         
                'stock' => 'sometimes|required|integer|min:0'     
            ]); 
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 400);
        }

        $product->update($request->all());      
        return response()->json([
            'success' => true,
            'data' => $product     
        ], 200); 
    }

    
    public function destroy(string $id)
    {
        $product = Product::find($id); 
        if (!$product) { 
            return response()->json([
                'success' => false, 
                'message' => 'Product not found'
            ], 404); 
        }
        $product->delete(); 
        return response()->noContent();
    }
}
