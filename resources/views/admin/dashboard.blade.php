<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão Administrativa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Acesso Rápido</h3>
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
            
            <!-- Filtros de Data -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros de Período</h3>
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                            <input type="date" id="start_date" name="start_date" 
                                   value="{{ $startDate }}"
                                   class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                            <input type="date" id="end_date" name="end_date" 
                                   value="{{ $endDate }}"
                                   class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cards de KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total de Usuários -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total de Usuários</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtos Ativos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Produtos Ativos</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $activeProducts }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Pedidos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total de Pedidos</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faturamento -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Faturamento</p>
                                <p class="text-2xl font-semibold text-green-600">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pedidos por Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pedidos por Status</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $ordersByStatus['pendente'] ?? 0 }}</div>
                            <div class="text-sm text-yellow-800">Pendentes</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $ordersByStatus['pago'] ?? 0 }}</div>
                            <div class="text-sm text-blue-800">Pagos</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $ordersByStatus['entregue'] ?? 0 }}</div>
                            <div class="text-sm text-green-800">Entregues</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $ordersByStatus['cancelado'] ?? 0 }}</div>
                            <div class="text-sm text-red-800">Cancelados</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Clientes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Clientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total de Pedidos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($topClients as $client)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $client->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $client->total_orders }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-green-600">R$ {{ number_format($client->total_spent, 2, ',', '.') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum cliente com pedidos pagos no período selecionado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Adicionais -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pedidos Recentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pedidos Recentes</h3>
                        <div class="space-y-3">
                            @forelse($recentOrders as $order)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">#{{ $order->order_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->user->name }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->formatted_total_amount }}</div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_badge_color }}">
                                            {{ $order->status_text }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Nenhum pedido recente.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Produtos com Estoque Baixo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Produtos com Estoque Baixo</h3>
                        <div class="space-y-3">
                            @forelse($lowStockProducts as $product)
                                <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $product->category->name }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-red-600">{{ $product->current_quantity }} un</div>
                                        <div class="text-xs text-gray-500">Mín: 10</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Todos os produtos com estoque adequado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
