<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pedido #') . $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informações do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informações do Pedido</h3>
                            <p class="text-sm text-gray-600">Criado em {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->statusBadgeColor }}">
                                {{ $order->statusText }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Cliente</h4>
                            <p class="text-sm text-gray-900">{{ $order->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Total do Pedido</h4>
                            <p class="text-2xl font-bold text-indigo-600">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Número do Pedido</h4>
                            <p class="text-sm font-mono text-gray-900">{{ $order->order_number }}</p>
                        </div>
                    </div>
                    
                    @if($order->notes)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Observações</h4>
                            <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Produtos do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Produtos do Pedido</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unitário</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($item->product->image_path)
                                                    <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                        <span class="text-gray-500 text-xs">Sem Imagem</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                                    <div class="text-sm text-gray-500">ID: {{ $item->product->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->product->category->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Fluxo de Status do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Fluxo de Status do Pedido</h3>
                    
                    <div class="relative">
                        <!-- Linha de progresso -->
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200"></div>
                        
                        <div class="relative flex justify-between">
                            <!-- Status: Pendente -->
                            <div class="flex flex-col items-center">
                                <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $order->status === 'pendente' ? 'bg-yellow-500 text-white' : ($order->status === 'cancelado' ? 'bg-gray-400 text-white' : 'bg-green-500 text-white') }}">
                                    @if($order->status === 'pendente')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-sm font-medium text-gray-900">Pendente</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            <!-- Status: Pago -->
                            <div class="flex flex-col items-center">
                                <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $order->status === 'pago' ? 'bg-blue-500 text-white' : (in_array($order->status, ['pendente', 'cancelado']) ? 'bg-gray-400 text-white' : 'bg-green-500 text-white') }}">
                                    @if($order->status === 'pago')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-sm font-medium text-gray-900">Pago</p>
                                    @if($order->paid_at)
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Status: Entregue -->
                            <div class="flex flex-col items-center">
                                <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $order->status === 'entregue' ? 'bg-green-500 text-white' : (in_array($order->status, ['pendente', 'pago', 'cancelado']) ? 'bg-gray-400 text-white' : 'bg-green-500 text-white') }}">
                                    @if($order->status === 'entregue')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-sm font-medium text-gray-900">Entregue</p>
                                    @if($order->delivered_at)
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->delivered_at)->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($order->cancelled_at)
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-red-900">Pedido Cancelado</p>
                                    <p class="text-sm text-red-600">{{ \Carbon\Carbon::parse($order->cancelled_at)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ações do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações</h3>
                    
                    <div class="flex flex-wrap gap-3">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('orders.edit', $order) }}" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Editar Pedido
                            </a>
                            
                            <!-- Botões de avanço de status -->
                            <div class="flex flex-wrap gap-2">
                                @if($order->status === 'pendente')
                                    <form action="{{ route('orders.advance-status', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="new_status" value="pago">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" onclick="return confirm('Confirmar pagamento do pedido?')">
                                            Confirmar Pagamento
                                        </button>
                                    </form>
                                @endif
                                
                                @if($order->status === 'pago')
                                    <form action="{{ route('orders.advance-status', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="new_status" value="entregue">
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            Marcar como Entregue
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Botão de retorno de status (apenas para admin) -->
                                @if(in_array($order->status, ['pago', 'entregue']))
                                    <form action="{{ route('orders.advance-status', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="new_status" value="pendente">
                                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" onclick="return confirm('Retornar pedido para status pendente?')">
                                            Retornar para Pendente
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Botão de cancelamento -->
                        @if(Auth::user()->role === 'admin' && in_array($order->status, ['pendente', 'pago']))
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                    Cancelar Pedido
                                </button>
                            </form>
                        @endif
                        
                        @if(Auth::user()->role === 'member' && $order->status === 'pendente')
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                    Cancelar Pedido
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
