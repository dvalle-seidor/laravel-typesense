<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Avanzada - Typesense</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">🔍 Búsqueda Avanzada con Typesense</h1>
        
        <!-- Formulario principal de búsqueda -->
        <div class="max-w-4xl mx-auto mb-8">
            <form action="/search" method="GET" class="bg-white rounded-lg shadow-md p-6">
                <div class="flex gap-2 mb-4">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $query }}"
                        placeholder="Buscar productos..." 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                    >
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold"
                    >
                        🔍 Buscar
                    </button>
                </div>
                
                <!-- Filtros avanzados -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 border-t pt-4">
                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $selectedCategory == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <select name="in_stock" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="true" {{ $selectedInStock === 'true' ? 'selected' : '' }}>En stock</option>
                            <option value="false" {{ $selectedInStock === 'false' ? 'selected' : '' }}>Agotado</option>
                        </select>
                    </div>
                    
                    <!-- Precio mínimo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Mínimo</label>
                        <input 
                            type="number" 
                            name="min_price" 
                            value="{{ $minPrice }}"
                            placeholder="0.00" 
                            step="0.01"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    
                    <!-- Precio máximo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Máximo</label>
                        <input 
                            type="number" 
                            name="max_price" 
                            value="{{ $maxPrice }}"
                            placeholder="999.99" 
                            step="0.01"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        Aplicar Filtros
                    </button>
                    <a href="/search" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-center">
                        Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>

        <!-- Estadísticas de búsqueda -->
        @if($query || $selectedCategory || $selectedInStock !== '' || $minPrice || $maxPrice)
            <div class="max-w-4xl mx-auto mb-6 bg-blue-50 rounded-lg p-4">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-semibold text-blue-800">Filtros activos:</span>
                    @if($query)
                        <span class="px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">
                            🔍 "{{ $query }}"
                        </span>
                    @endif
                    @if($selectedCategory)
                        <span class="px-3 py-1 bg-purple-200 text-purple-800 rounded-full text-sm">
                            📁 {{ $selectedCategory }}
                        </span>
                    @endif
                    @if($selectedInStock !== '')
                        <span class="px-3 py-1 bg-green-200 text-green-800 rounded-full text-sm">
                            📦 {{ $selectedInStock === 'true' ? 'En stock' : 'Agotado' }}
                        </span>
                    @endif
                    @if($minPrice)
                        <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">
                            💰 Desde ${{ number_format($minPrice, 2) }}
                        </span>
                    @endif
                    @if($maxPrice)
                        <span class="px-3 py-1 bg-orange-200 text-orange-800 rounded-full text-sm">
                            💰 Hasta ${{ number_format($maxPrice, 2) }}
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Resultados -->
        <div class="max-w-6xl mx-auto">
            @if($query || $selectedCategory || $selectedInStock !== '' || $minPrice || $maxPrice)
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        {{ $products->count() }} productos encontrados
                    </h2>
                    @if($products->count() === 0)
                        <p class="text-gray-600 mt-2">Intenta ajustar los filtros o términos de búsqueda</p>
                    @endif
                </div>
            @else
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-semibold text-gray-800">Todos los productos ({{ $products->count() }})</h2>
                </div>
            @endif

            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <!-- Badge de categoría -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-3 py-1 text-sm font-semibold">
                                {{ $product->category }}
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold mb-2 text-gray-800 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-gray-600 mb-4 text-sm line-clamp-3">{{ $product->description }}</p>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-2xl font-bold text-green-600">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->in_stock ? '✓ En stock' : '✗ Agotado' }}
                                    </span>
                                </div>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-semibold">
                                        🛒 Comprar
                                    </button>
                                    <button class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                                        ❤️
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">No se encontraron productos</h3>
                    <p class="text-gray-500 mb-6">Intenta con otros términos de búsqueda o ajusta los filtros</p>
                    <a href="/search" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors inline-block">
                        Ver todos los productos
                    </a>
                </div>
            @endif
        </div>

        <!-- Panel de información -->
        <div class="max-w-4xl mx-auto mt-12 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 border border-blue-200">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">🚀 Características de Typesense</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-blue-600 mb-2">⚡ Búsqueda Ultra Rápida</h3>
                    <p class="text-sm text-gray-600">Resultados instantáneos incluso con miles de productos</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-purple-600 mb-2">🎯 Búsqueda por Similitud</h3>
                    <p class="text-sm text-gray-600">Encuentra productos incluso con errores tipográficos</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-green-600 mb-2">🔤 Búsqueda Tipográfica</h3>
                    <p class="text-sm text-gray-600">Corrección automática de errores al escribir</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-orange-600 mb-2">📊 Filtros Avanzados</h3>
                    <p class="text-sm text-gray-600">Filtra por categoría, precio, stock y más</p>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    <strong>💡 Tip:</strong> Prueba buscar "lapto" (sin 'p'), "electro", "book" o usa los filtros de precio y categoría
                </p>
            </div>
        </div>
    </div>
</body>
</html>
