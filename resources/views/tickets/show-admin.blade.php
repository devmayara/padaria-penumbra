<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Ficha #{{ $ticket->ticket_number }}</h2>
                        <div class="flex space-x-2">
                            @if($ticket->pdf_path)
                                <a href="{{ route('tickets.download', $ticket) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    📥 Baixar PDF
                                </a>
                            @endif

                            <a href="{{ route('tickets.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Voltar
                            </a>
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
                                <p class="text-sm text-gray-600">Cliente:</p>
                                <p class="font-medium">{{ $ticket->order->user->name }} ({{ $ticket->order->user->email }})</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Data do Pedido:</p>
                                <p class="font-medium">{{ $ticket->order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total:</p>
                                <p class="font-medium text-lg text-green-600">{{ $ticket->order->formatted_total_amount }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ficha Gerada:</p>
                                <p class="font-medium">{{ $ticket->generated_at ? $ticket->generated_at->format('d/m/Y H:i') : 'Não gerada' }}</p>
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
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">QR Code</h3>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $ticket->qr_code_path) }}" 
                                     alt="QR Code da Ficha" 
                                     class="mx-auto border border-gray-300 rounded-lg"
                                     style="max-width: 200px;">
                                <p class="text-sm text-gray-600 mt-2">Escaneie para acessar a ficha digitalmente</p>
                            </div>
                        </div>
                    @endif

                    <!-- Notas -->
                    @if($ticket->notes)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Notas</h3>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-gray-800">{{ $ticket->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Ações Administrativas -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Ações Administrativas</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('orders.show', $ticket->order) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Ver Pedido
                            </a>
                            
                            @if($ticket->isGenerated())
                                <form method="POST" action="{{ route('tickets.regenerate', $ticket) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Tem certeza que deseja regenerar esta ficha?')">
                                        Regenerar Ficha
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
