<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Produto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Imagem do Produto -->
                        <div>
                            @if($product->image_path)
                                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                </div>
                            @else
                                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                                    <svg class="h-32 w-32 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Informações do Produto -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $product->name }}</h3>
                            </div>

                            <!-- Status -->
                            <div>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>

                            <!-- Informações Detalhadas -->
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Categoria</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Preço Unitário</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $product->formatted_price }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quantidade em Estoque</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="{{ $product->stock_status_color }}">
                                            {{ $product->current_quantity }} - {{ $product->stock_status_text }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->slug }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Data de Criação</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>

                            <!-- Atualização de Estoque -->
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Atualizar Estoque</h4>
                                <form method="POST" action="{{ route('products.update-stock', $product) }}" class="flex items-end space-x-4">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex-1">
                                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Nova Quantidade</label>
                                        <input type="number" id="stock_quantity" name="current_quantity" value="{{ $product->current_quantity }}" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Atualizar
                                    </button>
                                </form>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Voltar
                                </a>
                                
                                <a href="{{ route('products.edit', $product) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Editar
                                </a>
                                
                                <form method="POST" action="{{ route('products.toggle-status', $product) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        {{ $product->is_active ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
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
