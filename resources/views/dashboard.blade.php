<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->role === 'admin')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Gestão Administrativa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('users.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center">
                                Usuários
                            </a>
                            <a href="{{ route('categories.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-center">
                                Categorias
                            </a>
                            <a href="{{ route('products.index') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded text-center">
                                Produtos
                            </a>
                            <a href="{{ route('stock-movements.index') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded text-center">
                                Estoque
                            </a>
                            <a href="{{ route('orders.index') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded text-center">
                                Pedidos
                            </a>
                            <a href="{{ route('tickets.index') }}" class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-3 px-4 rounded text-center">
                                Fichas
                            </a>
                        </div>
                    </div>
                </div>
            @else
                @include('dashboard-member')
            @endif
        </div>
    </div>
</x-app-layout>
