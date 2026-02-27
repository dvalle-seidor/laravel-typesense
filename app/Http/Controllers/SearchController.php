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
        $priceRange = $request->get('price_range');

        // Predefined price ranges
        $priceRanges = [
            '0-50' => ['min' => 0, 'max' => 50, 'label' => '$0 - $50'],
            '50-200' => ['min' => 50, 'max' => 200, 'label' => '$50 - $200'],
            '200-1000' => ['min' => 200, 'max' => 1000, 'label' => '$200 - $1000'],
            '1000+' => ['min' => 1000, 'max' => 999999, 'label' => '$1000+']
        ];

        // Apply price range if selected
        if ($priceRange && isset($priceRanges[$priceRange])) {
            $range = $priceRanges[$priceRange];
            $minPrice = $range['min'];
            $maxPrice = $range['max'];
        }

        $products = collect();

        if (!empty($query) || !empty($category) || $inStock !== '' || $minPrice || $maxPrice) {
            \Log::info('Using Typesense search with filters', [
                'query' => $query,
                'category' => $category,
                'in_stock' => $inStock,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range' => $priceRange,
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

            // Build price filter manually to avoid Laravel Scout issues
            $priceFilter = [];
            if ($minPrice) {
                $priceFilter[] = "price:>={$minPrice}";
            }
            if ($maxPrice) {
                $priceFilter[] = "price:<={$maxPrice}";
            }
            
            // If we have price filters, we'll apply them after getting the search query
            $hasPriceFilter = !empty($priceFilter);

            $products = $searchQuery->get();
            
            // Apply additional price filtering if needed (fallback to database)
            if ($hasPriceFilter && $products->isNotEmpty()) {
                $filteredProducts = $products->filter(function ($product) use ($minPrice, $maxPrice) {
                    $price = (float) $product->price;
                    if ($minPrice && $price < $minPrice) return false;
                    if ($maxPrice && $price > $maxPrice) return false;
                    return true;
                });
                $products = $filteredProducts;
            }
        } else {
            \Log::info('Using Typesense search without filters', [
                'driver' => config('scout.driver')
            ]);
            // Get all products from database since Typesense search has default limits
            $products = Product::all();
        }

        // Get all categories for filter dropdown
        $categories = Product::distinct('category')->pluck('category');

        return view('search', compact('products', 'query', 'categories', 'category', 'inStock', 'minPrice', 'maxPrice', 'priceRange', 'priceRanges'))
            ->with('selectedCategory', $category)
            ->with('selectedInStock', $inStock);
    }
}
