<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Produto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Mensagens de erro -->
                        @if($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Nome -->
                        <div>
                            <x-input-label for="name" :value="__('Nome do Produto')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <x-input-label for="slug" :value="__('Slug (opcional)')" />
                            <input type="text" id="slug" name="slug" :value="old('slug')" placeholder="Deixe em branco para gerar automaticamente" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <!-- Categoria -->
                        <div>
                            <x-input-label for="category_id" :value="__('Categoria')" />
                            <select id="category_id" name="category_id" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Selecione uma categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Preço Unitário -->
                        <div>
                            <x-input-label for="unit_price" :value="__('Preço Unitário (R$)')" />
                            <input type="number" id="unit_price" name="unit_price" :value="old('unit_price')" step="0.01" min="0.01" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
                        </div>

                        <!-- Quantidade em Estoque -->
                        <div>
                            <x-input-label for="current_quantity" :value="__('Quantidade em Estoque')" />
                            <x-text-input id="current_quantity" class="block mt-1 w-full" type="number" name="current_quantity" :value="old('current_quantity', 0)" min="0" required />
                            <x-input-error :messages="$errors->get('current_quantity')" class="mt-2" />
                        </div>

                        <!-- Imagem -->
                        <div>
                            <x-input-label for="image" :value="__('Imagem do Produto')" />
                            <input type="file" id="image" name="image" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Formatos aceitos: JPEG, PNG, JPG, GIF. Tamanho máximo: 2MB.</p>
                        </div>

                        <!-- Status Ativo -->
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Produto ativo
                            </label>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Criar Produto') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
