@extends('layouts.member')

@section('content')
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Imagem do Produto -->
                        <div>
                            @if($product->image_path)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                     class="w-full h-96 object-cover rounded-lg shadow-lg">
                            @else
                                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400 text-lg">Sem imagem</span>
                                </div>
                            @endif
                        </div>

                        <!-- Informações do Produto -->
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                                <p class="text-lg text-gray-600">{{ $product->category->name }}</p>
                            </div>

                            <div class="flex items-center space-x-4">
                                <span class="text-4xl font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $product->stock_status_color }}">
                                    {{ $product->stock_status_text }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estoque Disponível</dt>
                                        <dd class="mt-1 text-lg text-gray-900">{{ $product->current_quantity }} unidades</dd>
                                    </div>
                                    

                                </dl>
                            </div>

                            @if($product->is_in_stock)
                                <div class="border-t border-gray-200 pt-4">
                                    <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}')" 
                                            class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg transition-colors">
                                        Adicionar
                                    </button>
                                </div>
                            @else
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Produto Indisponível</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p>Este produto não está disponível no momento.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal do Carrinho -->
    <div id="cartModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Adicionar ao Pedido</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Quantidade de <strong id="modalProductName"></strong>:</p>
                    <input type="number" id="quantity" min="1" value="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button onclick="closeCartModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button onclick="confirmAddToCart()" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Adicionar
                    </button>
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
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('cartModal').classList.remove('hidden');
        }

        function closeCartModal() {
            document.getElementById('cartModal').classList.add('hidden');
            currentProductId = null;
            currentProductName = '';
        }

        function confirmAddToCart() {
            const quantity = document.getElementById('quantity').value;
            if (quantity > 0) {
                window.location.href = `{{ route('member.orders.create') }}?product_id=${currentProductId}&quantity=${quantity}`;
            }
            closeCartModal();
        }

        // Fecha o modal ao clicar fora dele
        document.getElementById('cartModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCartModal();
            }
        });
    </script>
@endsection
