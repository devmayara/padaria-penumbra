@extends('layouts.member')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Ficha #{{ $ticket->ticket_number }}</h2>
                    <div class="flex space-x-2">
                        @if($ticket->pdf_path)
                            <a href="{{ route('member.tickets.download', $ticket) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                📥 Baixar PDF
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Status da Ficha -->
                <div class="mb-6">
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $ticket->status_color }}">
                            {{ $ticket->status_text }}
                        </span>
                                                
                        @if($ticket->printed_at)
                            <span class="text-sm text-gray-600">
                                Impresso em: {{ $ticket->printed_at->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Informações do Pedido -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Informações do Pedido</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Número do Pedido:</p>
                            <p class="font-medium">{{ $ticket->order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->order->status_badge_color }}">
                                {{ $ticket->order->status_text }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Data do Pedido:</p>
                            <p class="font-medium">{{ $ticket->order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total:</p>
                            <p class="font-medium text-lg text-green-600">{{ $ticket->order->formatted_total_amount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Itens do Pedido</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unit.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ticket->order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->product->category->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->product->formatted_price }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- QR Code -->
                @if($ticket->qr_code_path)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">QR Code da Ficha</h3>
                        <div class="flex justify-center">
                            <div class="bg-white p-4 rounded-lg border">
                                <img src="{{ $ticket->qr_code_url }}" alt="QR Code" class="w-32 h-32">
                                <p class="text-center text-sm text-gray-600 mt-2">{{ $ticket->ticket_number }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botão Voltar -->
                <div class="flex justify-start">
                    <a href="{{ route('member.orders.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        ← Voltar aos Meus Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
