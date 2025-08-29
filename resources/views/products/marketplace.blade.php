@extends('layouts.member')

@section('content')
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Filtrar Produtos</h3>
                    <form method="GET" action="{{ route('marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nome</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Nome do produto..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas as categorias</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Filtrar
                            </button>
                            <a href="{{ route('marketplace.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grid de Produtos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <!-- Imagem do Produto -->
                        <div class="aspect-w-1 aspect-h-1 w-full">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm">Sem Imagem</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Informações do Produto -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock_status_color }}">
                                    {{ $product->stock_status_text }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                            
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                                <span class="text-sm text-gray-600">Estoque: {{ $product->current_quantity }}</span>
                            </div>
                            
                            <!-- Botões de Ação -->
                            <div class="flex space-x-2">
                                <a href="{{ route('marketplace.show', $product) }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium transition-colors">
                                    Ver Detalhes
                                </a>
                                
                                @if($product->is_in_stock)
                                    <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}')" 
                                            class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                                        Adicionar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500 text-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p>Nenhum produto encontrado.</p>
                            <p class="text-sm">Tente ajustar os filtros de busca.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal do Carrinho -->
    <div id="cartModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Adicionar ao Pedido</h3>
                    
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                        <input type="number" id="quantity" min="1" value="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeCartModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                            Cancelar
                        </button>
                        <button onclick="confirmAddToCart()" 
                                class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-md font-medium">
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentProductId = null;
        let currentProductName = '';

        function addToCart(productId, productName) {
            currentProductId = productId;
            currentProductName = productName;
            document.getElementById('cartModal').classList.remove('hidden');
        }

        function closeCartModal() {
            document.getElementById('cartModal').classList.add('hidden');
        }

        function confirmAddToCart() {
            const quantity = document.getElementById('quantity').value;
            if (quantity > 0) {
                // Redireciona para criar pedido com o produto selecionado
                window.location.href = `{{ route('member.orders.create') }}?product_id=${currentProductId}&quantity=${quantity}`;
            }
            closeCartModal();
        }

        // Fecha modal ao clicar fora
        document.getElementById('cartModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCartModal();
            }
        });
    </script>
@endsection
