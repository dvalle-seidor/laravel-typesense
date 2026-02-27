<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        // Buscar sugerencias en Typesense
        $suggestions = Product::search($query)
            ->take(5)
            ->get(['id', 'name', 'category', 'price'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'price' => $product->price,
                    'highlight' => $product->name
                ];
            });
        
        return response()->json($suggestions);
    }
}
