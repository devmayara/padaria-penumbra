<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ Auth::user()->role === 'admin' ? 'Gestão de Pedidos' : 'Meus Pedidos' }}
            </h2>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('orders.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Novo Pedido
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtros para admin -->
                    @if(Auth::user()->role === 'admin')
                        <div class="mb-6">
                            <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Todos os status</option>
                                        <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="pago" {{ request('status') === 'pago' ? 'selected' : '' }}>Pago</option>
                                        <option value="preparando" {{ request('status') === 'preparando' ? 'selected' : '' }}>Preparando</option>
                                        <option value="entregue" {{ request('status') === 'entregue' ? 'selected' : '' }}>Entregue</option>
                                        <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                                    <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Todos os usuários</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="flex items-end">
                                    <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                        Filtrar
                                    </button>
                                    <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Limpar
                                    </a>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Mensagens de sucesso/erro -->
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

                    <!-- Tabela de pedidos -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Número
                                    </th>
                                    @if(Auth::user()->role === 'admin')
                                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Cliente
                                        </th>
                                    @endif
                                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->items->count() }} item(s)</div>
                                        </td>
                                        @if(Auth::user()->role === 'admin')
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $order->statusBadgeColor }}">
                                                {{ $order->statusText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col space-y-1">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                                        Ver
                                                    </a>
                                                    @if(Auth::user()->role === 'admin')
                                                        <a href="{{ route('orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            Editar
                                                        </a>
                                                    @endif
                                                </div>
                                                
                                                @if(Auth::user()->role === 'admin')
                                                    <!-- Ações rápidas de status -->
                                                    <div class="flex flex-wrap gap-1">
                                                        @if($order->status === 'pendente')
                                                            <form method="POST" action="{{ route('orders.advance-status', $order) }}" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="new_status" value="pago">
                                                                <button type="submit" class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded hover:bg-blue-200" onclick="return confirm('Confirmar pagamento?')">
                                                                    Pagar
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        @if($order->status === 'pago')
                                                            <form method="POST" action="{{ route('orders.advance-status', $order) }}" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="new_status" value="entregue">
                                                                <button type="submit" class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200">
                                                                    Entregar
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                @if(Auth::user()->role === 'admin' && in_array($order->status, ['pendente', 'pago']))
                                                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(Auth::user()->role === 'member' && $order->status === 'pendente')
                                                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->role === 'admin' ? '6' : '5' }}" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum pedido encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

