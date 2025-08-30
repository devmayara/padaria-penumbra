@extends('layouts.member')

@section('content')
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Informações do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Informações do Pedido
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $order->status_badge_color }}">
                            {{ $order->status_text }}
                        </span>
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Número do Pedido</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $order->order_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ficha do Pedido</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $order->ticket->ticket_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data de Criação</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</dd>
                        </div>
                        @if($order->notes)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Observações</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Fluxo de Status do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status do Pedido</h3>
                    <div class="relative">
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200"></div>
                        <div class="relative flex justify-between">
                            <!-- Pendente -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full {{ $order->status === 'pendente' ? 'bg-blue-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">1</span>
                                </div>
                                <span class="mt-2 text-sm font-medium {{ $order->status === 'pendente' ? 'text-blue-600' : 'text-gray-500' }}">Pendente</span>
                                @if($order->created_at)
                                    <span class="text-xs text-gray-400">{{ $order->created_at->format('d/m') }}</span>
                                @endif
                            </div>

                            <!-- Pago -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['pago', 'entregue']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">2</span>
                                </div>
                                <span class="mt-2 text-sm font-medium {{ in_array($order->status, ['pago', 'entregue']) ? 'text-green-600' : 'text-gray-500' }}">Pago</span>
                                @if($order->paid_at)
                                    <span class="text-xs text-gray-400">{{ $order->paid_at->format('d/m') }}</span>
                                @endif
                            </div>

                            <!-- Entregue -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full {{ $order->status === 'entregue' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">3</span>
                                </div>
                                <span class="mt-2 text-sm font-medium {{ $order->status === 'entregue' ? 'text-green-600' : 'text-gray-500' }}">Entregue</span>
                                @if($order->delivered_at)
                                    <span class="text-xs text-gray-400">{{ $order->delivered_at->format('d/m') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($order->cancelled_at)
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Pedido Cancelado</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Cancelado em: {{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Itens do Pedido</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
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
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $item->product->category->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ações do Pedido -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações</h3>
                    <div class="flex flex-wrap gap-3">
                        @if($order->status === 'pendente')
                            <form action="{{ route('member.orders.cancel', $order) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                    Cancelar Pedido
                                </button>
                            </form>
                        @endif
                        
                        <!-- Botão para visualizar ficha -->
                        @if($order->ticket)
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('member.tickets.show', $order->ticket) }}" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">
                                    Ver Ficha
                                </a>
                            </div>
                        @else
                            <span class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded-md text-sm">
                                ⚠️ Ficha não gerada
                            </span>
                        @endif
                        
                        <a href="{{ route('member.orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
