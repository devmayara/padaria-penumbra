<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalhes da Movimentação
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Editar
                </a>
                <a href="{{ route('stock-movements.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Voltar
                </a>
            </div>
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

                    <!-- Informações da Movimentação -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Movimentação</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tipo:</span>
                                    <span class="inline-flex ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $stockMovement->type_badge_color }}">
                                        {{ $stockMovement->type_text }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Quantidade:</span>
                                    <span class="ml-2 text-sm text-gray-900 {{ $stockMovement->type_color }}">
                                        {{ $stockMovement->quantity_with_sign }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Preço Unitário:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $stockMovement->formatted_total_value }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Valor Total:</span>
                                    <span class="ml-2 text-sm font-semibold text-gray-900">
                                        {{ $stockMovement->formatted_total_value }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Data:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $stockMovement->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Usuário:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $stockMovement->user->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Produto</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Produto:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $stockMovement->product->name }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Categoria:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $stockMovement->product->category->name }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Estoque Atual:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $stockMovement->product->current_quantity }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Status do Estoque:</span>
                                    <span class="ml-2 text-sm {{ $stockMovement->product->stock_status_color }}">
                                        {{ $stockMovement->product->stock_status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motivo e Observações -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes Adicionais</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Motivo:</span>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $stockMovement->reason ?: 'Não informado' }}
                                </p>
                            </div>
                            
                            <div>
                                <span class="text-sm font-medium text-gray-500">Observações:</span>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $stockMovement->notes ?: 'Nenhuma observação' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                Criado em: {{ $stockMovement->created_at->format('d/m/Y H:i:s') }}
                                @if($stockMovement->updated_at != $stockMovement->created_at)
                                    • Atualizado em: {{ $stockMovement->updated_at->format('d/m/Y H:i:s') }}
                                @endif
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Editar Movimentação
                                </a>
                                
                                <form method="POST" action="{{ route('stock-movements.destroy', $stockMovement) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
