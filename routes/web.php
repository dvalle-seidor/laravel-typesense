<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/search', function () {
    $query = request('q', '');
    $category = request('category', '');
    $inStock = request('in_stock', '');
    $minPrice = request('min_price', '');
    $maxPrice = request('max_price', '');
    
    if ($query || $category || $inStock !== '' || $minPrice || $maxPrice) {
        $searchQuery = Product::search($query);
        
        // Aplicar filtros adicionales
        if ($category) {
            $searchQuery->where('category', $category);
        }
        
        if ($inStock !== '') {
            $searchQuery->where('in_stock', $inStock === 'true');
        }
        
        if ($minPrice) {
            $searchQuery->where('price', '>=', (float) $minPrice);
        }
        
        if ($maxPrice) {
            $searchQuery->where('price', '<=', (float) $maxPrice);
        }
        
        $products = $searchQuery->get();
    } else {
        $products = Product::all();
    }
    
    // Obtener categorías para el filtro
    $categories = Product::distinct('category')->pluck('category');
    
    return view('search', [
        'products' => $products,
        'query' => $query,
        'categories' => $categories,
        'selectedCategory' => $category,
        'selectedInStock' => $inStock,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice
    ]);
});

Route::get('/import', function () {
    Product::makeAllSearchable();
    return 'Products imported to Typesense successfully!';
});
