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
                            <a href="{{ route('admin.users.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center">
                                Usuários
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-center">
                                Categorias
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded text-center">
                                Produtos
                            </a>
                            <a href="{{ route('admin.stock-movements.index') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded text-center">
                                Estoque
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded text-center">
                                Pedidos
                            </a>
                            <a href="{{ route('admin.tickets.index') }}" class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-3 px-4 rounded text-center">
                                Fichas
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-4 rounded text-center">
                                Relatórios
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Área do Cliente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <a href="{{ route('member.marketplace') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-center shadow-md hover:shadow-lg transition-all">
                                <div class="text-2xl mb-2">🛍️</div>
                                <div class="font-semibold">Catálogo de Produtos</div>
                                <div class="text-sm opacity-90">Explore nossos produtos</div>
                            </a>
                            <a href="{{ route('member.orders.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg text-center shadow-md hover:shadow-lg transition-all">
                                <div class="text-2xl mb-2">🛒</div>
                                <div class="font-semibold">Fazer Novo Pedido</div>
                                <div class="text-sm opacity-90">Crie seu pedido</div>
                            </a>
                            <a href="{{ route('member.orders.index') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-lg text-center shadow-md hover:shadow-lg transition-all">
                                <div class="text-2xl mb-2">📋</div>
                                <div class="font-semibold">Meus Pedidos</div>
                                <div class="text-sm opacity-90">Acompanhe seus pedidos</div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
