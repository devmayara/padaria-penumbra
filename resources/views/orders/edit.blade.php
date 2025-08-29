<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Pedido #') . $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Informações do Pedido</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cliente *
                                    </label>
                                    <select 
                                        name="user_id" 
                                        id="user_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    >
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $order->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="pendente" {{ $order->status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="pago" {{ $order->status === 'pago' ? 'selected' : '' }}>Pago</option>
                                        <option value="entregue" {{ $order->status === 'entregue' ? 'selected' : '' }}>Entregue</option>
                                        <option value="cancelado" {{ $order->status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Número do Pedido
                                        </label>
                                        <input type="text" value="{{ $order->order_number }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Total
                                        </label>
                                        <input type="text" value="R$ {{ number_format($order->total_amount, 2, ',', '.') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Observações
                                </label>
                                <textarea 
                                    name="notes" 
                                    id="notes" 
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Observações sobre o pedido..."
                                >{{ $order->notes }}</textarea>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Produtos do Pedido</h3>
                            
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
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $item->product->category->name }}</div>
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

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Histórico do Pedido</h3>
                            
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">Criado em:</span>
                                    <span class="text-sm font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                
                                @if($order->paid_at)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">Pago em:</span>
                                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                                
                                @if($order->delivered_at)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">Entregue em:</span>
                                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($order->delivered_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                                
                                @if($order->cancelled_at)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">Cancelado em:</span>
                                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($order->cancelled_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Atualizar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
