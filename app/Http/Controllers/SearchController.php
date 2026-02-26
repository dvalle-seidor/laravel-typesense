<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Force check that we're using Typesense
        if (config('scout.driver') !== 'typesense') {
            \Log::error('Scout driver is not typesense: ' . config('scout.driver'));
            abort(500, 'Search engine not configured properly');
        }

        $query = $request->get('q', '');
        $category = $request->get('category', '');
        $inStock = $request->get('in_stock', '');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $products = collect();

        if (!empty($query) || !empty($category) || $inStock !== '' || $minPrice || $maxPrice) {
            \Log::info('Using Typesense search with filters', [
                'query' => $query,
                'category' => $category,
                'in_stock' => $inStock,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'driver' => config('scout.driver')
            ]);
            
            $searchQuery = Product::search($query);

            // Apply filters
            if (!empty($category)) {
                $searchQuery->where('category', $category);
            }

            if ($inStock === 'true') {
                $searchQuery->where('in_stock', true);
            } elseif ($inStock === 'false') {
                $searchQuery->where('in_stock', false);
            }

            if ($minPrice) {
                $searchQuery->where('price', '>=', (float) $minPrice);
            }

            if ($maxPrice) {
                $searchQuery->where('price', '<=', (float) $maxPrice);
            }

            $products = $searchQuery->get();
        } else {
            \Log::info('Using Typesense search without filters', [
                'driver' => config('scout.driver')
            ]);
            // Get all products from database since Typesense search has default limits
            $products = Product::all();
        }

        // Get all categories for filter dropdown
        $categories = Product::distinct('category')->pluck('category');

        return view('search', compact('products', 'query', 'categories', 'minPrice', 'maxPrice'))
            ->with('selectedCategory', $category)
            ->with('selectedInStock', $inStock);
    }
}
