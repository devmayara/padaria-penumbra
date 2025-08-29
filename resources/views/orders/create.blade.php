<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Novo Pedido') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Informações do Pedido</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cliente *
                                    </label>
                                    <select 
                                        name="user_id" 
                                        id="user_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    >
                                        <option value="">Selecione um cliente</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Observações
                                    </label>
                                    <textarea 
                                        name="notes" 
                                        id="notes" 
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Observações sobre o pedido..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Produtos do Pedido</h3>
                            
                            <div id="productsContainer">
                                <div class="product-item border border-gray-200 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Produto
                                            </label>
                                            <select 
                                                name="products[]" 
                                                class="product-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                required
                                            >
                                                <option value="">Selecione um produto</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            data-price="{{ $product->unit_price }}"
                                                            data-stock="{{ $product->current_quantity }}">
                                                        {{ $product->name }} - R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Quantidade
                                            </label>
                                            <input 
                                                type="number" 
                                                name="quantities[]" 
                                                min="1" 
                                                class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                required
                                            >
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Preço Unitário
                                            </label>
                                            <input 
                                                type="text" 
                                                name="unit_prices[]" 
                                                class="unit-price-input w-full px-3 py-2 border border-gray-300 rounded-md rounded-md bg-gray-50"
                                                readonly
                                            >
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Total
                                            </label>
                                            <input 
                                                type="text" 
                                                class="total-price-input w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <button type="button" class="remove-product text-red-600 hover:text-red-800 text-sm font-medium">
                                            Remover Produto
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" id="addProduct" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                + Adicionar Produto
                            </button>
                        </div>

                        <div class="mb-6">
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold">Total do Pedido:</span>
                                    <span class="text-2xl font-bold text-indigo-600" id="orderTotal">R$ 0,00</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
                productItem.querySelector('.quantity-input').value = '';
                productItem.querySelector('.unit-price-input').value = '';
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
                const unitPriceInput = productItem.querySelector('.unit-price-input');
                const totalInput = productItem.querySelector('.total-price-input');
                
                if (select.value && quantityInput.value) {
                    const option = select.querySelector(`option[value="${select.value}"]`);
                    const price = parseFloat(option.dataset.price);
                    const quantity = parseInt(quantityInput.value);
                    
                    unitPriceInput.value = `R$ ${price.toFixed(2).replace('.', ',')}`;
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
            
            // Validação do formulário
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                const userId = document.getElementById('user_id').value;
                if (!userId) {
                    e.preventDefault();
                    alert('Por favor, selecione um cliente para o pedido.');
                    return false;
                }
                
                // Verificar se pelo menos um produto foi selecionado
                const productSelects = document.querySelectorAll('.product-select');
                let hasProduct = false;
                productSelects.forEach(select => {
                    if (select.value) hasProduct = true;
                });
                
                if (!hasProduct) {
                    e.preventDefault();
                    alert('Por favor, selecione pelo menos um produto para o pedido.');
                    return false;
                }
            });
            
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
</x-app-layout>
