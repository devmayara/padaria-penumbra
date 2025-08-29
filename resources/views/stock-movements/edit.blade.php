<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Movimentação de Estoque
            </h2>
            <a href="{{ route('stock-movements.show', $stockMovement) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Informações atuais -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-blue-900 mb-3">Movimentação Atual</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-blue-700">Produto:</span>
                                <span class="ml-2 text-blue-900">{{ $stockMovement->product->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">Tipo:</span>
                                <span class="inline-flex ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $stockMovement->type_badge_color }}">
                                    {{ $stockMovement->type_text }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">Quantidade:</span>
                                <span class="ml-2 text-blue-900 {{ $stockMovement->type_color }}">
                                    {{ $stockMovement->quantity_with_sign }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('stock-movements.update', $stockMovement) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Movimentação *
                                </label>
                                <select id="type" name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="entrada" {{ $stockMovement->type === 'entrada' ? 'selected' : '' }}>
                                        Entrada
                                    </option>
                                    <option value="saida" {{ $stockMovement->type === 'saida' ? 'selected' : '' }}>
                                        Saída
                                    </option>
                                    <option value="ajuste" {{ $stockMovement->type === 'ajuste' ? 'selected' : '' }}>
                                        Ajuste
                                    </option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantidade *
                                </label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $stockMovement->quantity) }}" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Preço Unitário (opcional)
                                </label>
                                <input type="number" id="unit_price" name="unit_price" value="{{ old('unit_price', $stockMovement->unit_price) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                                @error('unit_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Motivo *
                                </label>
                                <input type="text" id="reason" name="reason" value="{{ old('reason', $stockMovement->reason) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: Compra, Venda, Ajuste de inventário">
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Observações
                            </label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Observações adicionais sobre a movimentação">{{ old('notes', $stockMovement->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Atenção
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>
                                            Ao editar esta movimentação, o estoque do produto será recalculado automaticamente. 
                                            Certifique-se de que as informações estão corretas antes de salvar.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('stock-movements.show', $stockMovement) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Atualizar Movimentação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
