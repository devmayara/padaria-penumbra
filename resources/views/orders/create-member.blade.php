@extends('layouts.member')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Criar Novo Pedido</h2>
                </div>

                <!-- Campo de observações -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Observações (opcional)</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Observações sobre o pedido..."></textarea>
                </div>

                <form action="{{ route('member.orders.store') }}" method="POST" id="orderForm">
                    @csrf
                    
                    <!-- Lista de produtos -->
                    <div id="productsContainer">
                        <div class="product-item border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produto *</label>
                                    <select name="products[]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-price="{{ $product->unit_price }}" 
                                                    data-stock="{{ $product->current_quantity }}"
                                                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} - R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                                    <input type="number" name="quantities[]" min="1" value="{{ request('quantity', 1) }}" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                                    <input type="text" class="total-price-input w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="button" class="remove-product text-red-600 hover:text-red-800 text-sm font-medium">Remover Produto</button>
                            </div>
                        </div>
                    </div>

                    <!-- Botão para adicionar mais produtos -->
                    <div class="mb-6">
                        <button type="button" id="addProduct" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            + Adicionar Produto
                        </button>
                    </div>

                    <!-- Total do pedido -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="text-lg font-semibold text-gray-900">
                            Total do Pedido: <span id="orderTotal">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('member.orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Criar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsContainer = document.getElementById('productsContainer');
        const addProductBtn = document.getElementById('addProduct');
        const orderTotalSpan = document.getElementById('orderTotal');
        
        // Adicionar novo produto
        addProductBtn.addEventListener('click', function() {
            const productItem = productsContainer.querySelector('.product-item').cloneNode(true);
            
            // Limpar valores
            productItem.querySelector('.product-select').value = '';
            productItem.querySelector('.quantity-input').value = '1';
            productItem.querySelector('.total-price-input').value = '';
            
            // Adicionar ao container
            productsContainer.appendChild(productItem);
            
            // Reativar eventos
            attachProductEvents(productItem);
        });
        
        // Remover produto
        productsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                if (productsContainer.querySelectorAll('.product-item').length > 1) {
                    e.target.closest('.product-item').remove();
                    calculateOrderTotal();
                }
            }
        });
        
        // Calcular preços
        productsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
                calculateProductTotal(e.target.closest('.product-item'));
                calculateOrderTotal();
            }
        });
        
        // Calcular total do produto
        function calculateProductTotal(productItem) {
            const select = productItem.querySelector('.product-select');
            const quantityInput = productItem.querySelector('.quantity-input');
            const totalInput = productItem.querySelector('.total-price-input');
            
            if (select.value && quantityInput.value) {
                const option = select.querySelector(`option[value="${select.value}"]`);
                const price = parseFloat(option.dataset.price);
                const quantity = parseInt(quantityInput.value);
                
                totalInput.value = `R$ ${(price * quantity).toFixed(2).replace('.', ',')}`;
            }
        }
        
        // Calcular total do pedido
        function calculateOrderTotal() {
            let total = 0;
            const totalInputs = productsContainer.querySelectorAll('.total-price-input');
            
            totalInputs.forEach(input => {
                const value = input.value.replace('R$ ', '').replace(',', '.');
                if (value && !isNaN(value)) {
                    total += parseFloat(value);
                }
            });
            
            orderTotalSpan.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        // Ativar eventos para o primeiro produto
        attachProductEvents(productsContainer.querySelector('.product-item'));
        
        // Calcular total inicial se produto pré-selecionado
        if (document.querySelector('.product-select').value) {
            calculateProductTotal(productsContainer.querySelector('.product-item'));
            calculateOrderTotal();
        }
        
        function attachProductEvents(productItem) {
            const select = productItem.querySelector('.product-select');
            const quantityInput = productItem.querySelector('.quantity-input');
            
            select.addEventListener('change', function() {
                if (this.value) {
                    const option = this.querySelector(`option[value="${this.value}"]`);
                    const stock = parseInt(option.dataset.stock);
                    quantityInput.max = stock;
                    quantityInput.placeholder = `Máx: ${stock}`;
                }
            });
        }
    });
</script>
@endsection
