<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bem-vindo, {{ Auth::user()->name }}!</h3>
                    
                    @if(Auth::user()->role === 'admin')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                                <h4 class="text-lg font-medium text-blue-900 mb-2">Gestão de Usuários</h4>
                                <p class="text-blue-700 mb-4">Gerencie usuários, perfis e permissões</p>
                                <a href="{{ route('users.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Acessar
                                </a>
                            </div>
                            
                            <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                                <h4 class="text-lg font-medium text-green-900 mb-2">Gestão de Produtos</h4>
                                <p class="text-green-700 mb-4">Gerencie produtos e estoque</p>
                                <a href="#" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Em breve
                                </a>
                            </div>
                            
                            <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                                <h4 class="text-lg font-medium text-purple-900 mb-2">Relatórios</h4>
                                <p class="text-purple-700 mb-4">Visualize relatórios e estatísticas</p>
                                <a href="#" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Em breve
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Área do Cliente</h4>
                            <p class="text-gray-700">Em breve você poderá fazer pedidos e acompanhar suas compras.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
