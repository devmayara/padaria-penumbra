<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Ticket;
use App\Services\QrCodeService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Apenas admins podem acessar a gestão de pedidos
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem acessar a gestão de pedidos.');
        }

        $query = Order::with(['user', 'items.product.category']);

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por usuário
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por data
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $users = \App\Models\User::where('role', 'member')->orderBy('name')->get();

        return view('orders.index', compact('orders', 'users'));
    }

    /**
     * Display a listing of orders for members.
     */
    public function memberIndex(Request $request)
    {
        // Apenas membros podem acessar seus próprios pedidos
        if (Auth::user()->role !== 'member') {
            abort(403, 'Acesso negado. Apenas membros podem acessar seus pedidos.');
        }

        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('orders.index-member', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Apenas admins podem acessar a criação de pedidos
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem criar pedidos.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $users = \App\Models\User::where('role', 'member')->orderBy('name')->get();

        return view('orders.create', compact('products', 'users'));
    }

    /**
     * Show the form for creating a new order for members.
     */
    public function memberCreate()
    {
        // Apenas membros podem acessar a criação de seus próprios pedidos
        if (Auth::user()->role !== 'member') {
            abort(403, 'Acesso negado. Apenas membros podem criar seus próprios pedidos.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('orders.create-member', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Apenas admins podem criar pedidos
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem criar pedidos.');
        }

        $request->validate([
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'user_id' => 'required|exists:users,id',
        ], [
            'products.required' => 'Selecione pelo menos um produto.',
            'products.*.exists' => 'Um dos produtos selecionados não existe.',
            'quantities.required' => 'Informe a quantidade para cada produto.',
            'quantities.*.integer' => 'A quantidade deve ser um número inteiro.',
            'quantities.*.min' => 'A quantidade deve ser maior que zero.',
            'user_id.required' => 'Selecione um cliente para o pedido.',
            'user_id.exists' => 'O cliente selecionado não existe.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Calcula o total do pedido
                $totalAmount = 0;
                $items = [];

                foreach ($request->products as $index => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = $request->quantities[$index];

                    // Verifica se há estoque suficiente
                    if ($product->current_quantity < $quantity) {
                        throw new \Exception("Produto '{$product->name}' não possui estoque suficiente. Disponível: {$product->current_quantity}, Solicitado: {$quantity}");
                    }

                    // Verifica se o produto está ativo
                    if (!$product->is_active) {
                        throw new \Exception("Produto '{$product->name}' não está ativo e não pode ser incluído em pedidos.");
                    }

                    $totalPrice = $product->unit_price * $quantity;

                    $totalAmount += $totalPrice;

                    $items[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'unit_price' => $product->unit_price,
                        'total_price' => $totalPrice,
                    ];
                }

                // Cria o pedido
                $order = Order::create([
                    'user_id' => $request->user_id,
                    'order_number' => Order::generateOrderNumber(),
                    'status' => 'pendente',
                    'total_amount' => $totalAmount,
                    'notes' => $request->notes,
                ]);

                // Cria os itens do pedido
                foreach ($items as $item) {
                    $order->items()->create($item);
                }

                // Gera ficha automaticamente para o pedido
                $this->generateTicketForOrder($order);
            });

            return redirect()->route('orders.show', Order::latest()->first())
                ->with('success', 'Pedido criado com sucesso! Ficha gerada automaticamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar pedido: '.$e->getMessage());
        }
    }

    /**
     * Store a newly created order for members.
     */
    public function memberStore(Request $request)
    {
        // Apenas membros podem criar seus próprios pedidos
        if (Auth::user()->role !== 'member') {
            abort(403, 'Acesso negado. Apenas membros podem criar seus próprios pedidos.');
        }

        $request->validate([
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ], [
            'products.required' => 'Selecione pelo menos um produto.',
            'products.*.exists' => 'Um dos produtos selecionados não existe.',
            'quantities.required' => 'Informe a quantidade para cada produto.',
            'quantities.*.integer' => 'A quantidade deve ser um número inteiro.',
            'quantities.*.min' => 'A quantidade deve ser maior que zero.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Calcula o total do pedido
                $totalAmount = 0;
                $items = [];

                foreach ($request->products as $index => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = $request->quantities[$index];

                    // Verifica se há estoque suficiente
                    if ($product->current_quantity < $quantity) {
                        throw new \Exception("Produto '{$product->name}' não possui estoque suficiente. Disponível: {$product->current_quantity}, Solicitado: {$quantity}");
                    }

                    // Verifica se o produto está ativo
                    if (!$product->is_active) {
                        throw new \Exception("Produto '{$product->name}' não está ativo e não pode ser incluído em pedidos.");
                    }

                    $totalPrice = $product->unit_price * $quantity;

                    $totalAmount += $totalPrice;

                    $items[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'unit_price' => $product->unit_price,
                        'total_price' => $totalPrice,
                    ];
                }

                // Cria o pedido
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => Order::generateOrderNumber(),
                    'status' => 'pendente',
                    'total_amount' => $totalAmount,
                    'notes' => $request->notes,
                ]);

                // Cria os itens do pedido
                foreach ($items as $item) {
                    $order->items()->create($item);
                }

                // Gera ficha automaticamente para o pedido
                $this->generateTicketForOrder($order);
            });

            return redirect()->route('member.orders.show', Order::latest()->first())
                ->with('success', 'Pedido criado com sucesso! Ficha gerada automaticamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar pedido: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Verifica se o usuário pode ver o pedido
        // if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
        //     abort(403);
        // }

        $order->load(['user', 'items.product.category']);

        // Retorna view diferente baseado no role
        if (Auth::user()->role === 'admin') {
            return view('orders.show', compact('order'));
        } else {
            return view('orders.show-member', compact('order'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Apenas admins podem editar pedidos
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem editar pedidos.');
        }

        $order->load(['user', 'items.product.category']);
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $users = \App\Models\User::where('role', 'member')->orderBy('name')->get();

        return view('orders.edit', compact('order', 'products', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Apenas admins podem atualizar pedidos
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem atualizar pedidos.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pendente,pago,entregue,cancelado',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                $oldStatus = $order->status;
                $newStatus = $request->status;

                // Atualiza o pedido
                $order->update([
                    'user_id' => $request->user_id,
                    'status' => $newStatus,
                    'notes' => $request->notes,
                ]);

                // Atualiza timestamps específicos
                if ($newStatus === 'pago' && $oldStatus !== 'pago') {
                    $order->update(['paid_at' => now()]);
                } elseif ($newStatus === 'entregue' && $oldStatus !== 'entregue') {
                    $order->update(['delivered_at' => now()]);
                } elseif ($newStatus === 'cancelado' && $oldStatus !== 'cancelado') {
                    $order->update(['cancelled_at' => now()]);
                }

                // Gerencia o estoque baseado na mudança de status
                if ($newStatus === 'pago' && $oldStatus === 'pendente') {
                    // Saída de estoque ao pagar
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        $product->decrement('current_quantity', $item->quantity);

                        // Registra movimentação de saída
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $order->user_id,
                            'type' => 'saida',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'reason' => 'Venda - Pedido #'.$order->order_number,
                            'notes' => 'Saída automática ao confirmar pagamento',
                        ]);
                    }
                } elseif ($newStatus === 'cancelado' && in_array($oldStatus, ['pendente', 'pago'])) {
                    // Estorno de estoque ao cancelar (se já foi pago)
                    if ($oldStatus === 'pago') {
                        foreach ($order->items as $item) {
                            $product = $item->product;
                            $product->increment('current_quantity', $item->quantity);

                            // Registra movimentação de entrada (estorno)
                            StockMovement::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::id(),
                                'type' => 'entrada',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'reason' => 'Cancelamento - Pedido #'.$order->order_number,
                                'notes' => 'Estorno automático ao cancelar pedido pago',
                            ]);
                        }
                    }
                } elseif ($newStatus === 'entregue' && $oldStatus === 'pago') {
                    // Pedido entregue - não afeta estoque (já foi debitado)
                    // Registra apenas a mudança de status
                } elseif ($newStatus === 'pendente' && in_array($oldStatus, ['pago', 'entregue'])) {
                    // Retorno para pendente - estorna estoque se já foi pago
                    if ($oldStatus === 'pago') {
                        foreach ($order->items as $item) {
                            $product = $item->product;
                            $product->increment('current_quantity', $item->quantity);

                            // Registra movimentação de entrada (estorno)
                            StockMovement::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::id(),
                                'type' => 'entrada',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'reason' => 'Retorno para Pendente - Pedido #'.$order->order_number,
                                'notes' => 'Estorno automático ao retornar pedido pago para pendente',
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pedido atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar pedido: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Apenas admins podem excluir pedidos
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem excluir pedidos.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Se o pedido foi pago, estorna o estoque
                if ($order->status === 'pago') {
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        $product->increment('current_quantity', $item->quantity);

                        // Registra movimentação de entrada (estorno)
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'type' => 'entrada',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'reason' => 'Exclusão - Pedido #'.$order->order_number,
                            'notes' => 'Estorno automático ao excluir pedido pago',
                        ]);
                    }
                }

                $order->delete();
            });

            return redirect()->route('orders.index')
                ->with('success', 'Pedido excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir pedido: '.$e->getMessage());
        }
    }

    /**
     * Cancel an order (for members).
     */
    public function cancel(Order $order)
    {
        // Apenas o dono do pedido ou admin pode cancelar
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403);
        }

        // Membro só pode cancelar pedidos pendentes
        if (Auth::user()->role === 'member' && $order->status !== 'pendente') {
            return back()->with('error', 'Você só pode cancelar pedidos pendentes.');
        }

        // Admin pode cancelar pedidos pendentes ou pagos
        if (Auth::user()->role === 'admin' && !in_array($order->status, ['pendente', 'pago'])) {
            return back()->with('error', 'Apenas pedidos pendentes ou pagos podem ser cancelados.');
        }

        try {
            DB::transaction(function () use ($order) {
                $oldStatus = $order->status;

                $order->update([
                    'status' => 'cancelado',
                    'cancelled_at' => now(),
                ]);

                // Se o pedido foi pago, estorna o estoque
                if ($oldStatus === 'pago') {
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        $product->increment('current_quantity', $item->quantity);

                        // Registra movimentação de entrada (estorno)
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'type' => 'entrada',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'reason' => 'Cancelamento - Pedido #'.$order->order_number,
                            'notes' => 'Estorno automático ao cancelar pedido pago',
                        ]);
                    }
                }
            });

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pedido cancelado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cancelar pedido: '.$e->getMessage());
        }
    }

    /**
     * Mark order as delivered (admin only).
     */
    public function markAsDelivered(Order $order)
    {
        // Apenas admins podem marcar como entregue
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem marcar pedidos como entregues.');
        }

        if ($order->status !== 'pago') {
            return back()->with('error', 'Apenas pedidos pagos podem ser marcados como entregues.');
        }

        $order->update([
            'status' => 'entregue',
            'delivered_at' => now(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pedido marcado como entregue!');
    }

    /**
     * Advance order status (admin only).
     */
    public function advanceStatus(Request $request, Order $order)
    {
        // Apenas admins podem avançar status
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem alterar o status dos pedidos.');
        }

        $request->validate([
            'new_status' => 'required|in:pendente,pago,entregue',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->new_status;

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus) {
                // Atualiza o status
                $order->update(['status' => $newStatus]);

                // Atualiza timestamps específicos
                if ($newStatus === 'pago' && $oldStatus !== 'pago') {
                    $order->update(['paid_at' => now()]);
                } elseif ($newStatus === 'entregue' && $oldStatus !== 'entregue') {
                    $order->update(['delivered_at' => now()]);
                }

                // Gerencia o estoque baseado na mudança de status
                if ($newStatus === 'pago' && $oldStatus === 'pendente') {
                    // Saída de estoque ao pagar
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        $product->decrement('current_quantity', $item->quantity);

                        // Registra movimentação de saída
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $order->user_id,
                            'type' => 'saida',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'reason' => 'Venda - Pedido #'.$order->order_number,
                            'notes' => 'Saída automática ao confirmar pagamento',
                        ]);
                    }
                } elseif ($newStatus === 'pendente' && in_array($oldStatus, ['pago', 'entregue'])) {
                    // Retorno para pendente - estorna estoque se já foi pago
                    if ($oldStatus === 'pago') {
                        foreach ($order->items as $item) {
                            $product = $item->product;
                            $product->increment('current_quantity', $item->quantity);

                            // Registra movimentação de entrada (estorno)
                            StockMovement::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::id(),
                                'type' => 'entrada',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'reason' => 'Retorno para Pendente - Pedido #'.$order->order_number,
                                'notes' => 'Estorno automático ao retornar pedido pago para pendente',
                            ]);
                        }
                    }
                }
            });

            $statusMessages = [
                'pendente' => 'Pedido retornado para status pendente',
                'pago' => 'Pagamento confirmado com sucesso',
                'entregue' => 'Pedido marcado como entregue',
            ];

            return redirect()->route('orders.show', $order)
                ->with('success', $statusMessages[$newStatus].'!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao alterar status do pedido: '.$e->getMessage());
        }
    }

    /**
     * Display the specified order for members.
     */
    public function memberShow(Order $order)
    {
        // Apenas membros podem acessar seus próprios pedidos
        if (Auth::user()->role !== 'member') {
            abort(403, 'Acesso negado. Apenas membros podem acessar seus pedidos.');
        }

        // Verifica se o pedido pertence ao usuário logado
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado. Este pedido não pertence a você.');
        }

        $order->load(['items.product.category']);

        return view('orders.show-member', compact('order'));
    }

    /**
     * Cancel an order for members.
     */
    public function memberCancel(Order $order)
    {
        // Apenas membros podem cancelar seus próprios pedidos
        if (Auth::user()->role !== 'member') {
            abort(403, 'Acesso negado. Apenas membros podem cancelar seus pedidos.');
        }

        // Verifica se o pedido pertence ao usuário logado
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado. Este pedido não pertence a você.');
        }

        // Membro só pode cancelar pedidos pendentes
        if ($order->status !== 'pendente') {
            return back()->with('error', 'Você só pode cancelar pedidos pendentes.');
        }

        try {
            DB::transaction(function () use ($order) {
                $order->update([
                    'status' => 'cancelado',
                    'cancelled_at' => now(),
                ]);
            });

            return redirect()->route('member.orders.index')
                ->with('success', 'Pedido cancelado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cancelar pedido: '.$e->getMessage());
        }
    }

    /**
     * Generate ticket for an order automatically.
     */
    private function generateTicketForOrder(Order $order)
    {
        try {
            // Gera número único da ficha
            $ticketNumber = Ticket::generateTicketNumber();
            
            // Cria a ficha
            $ticket = Ticket::create([
                'order_id' => $order->id,
                'ticket_number' => $ticketNumber,
                'status' => 'pendente',
                'notes' => 'Ficha gerada automaticamente ao criar pedido',
            ]);

            // Gera QR code
            $qrCodeService = app(QrCodeService::class);
            $qrData = $qrCodeService->generateTicketData($ticketNumber, $order->id);
            $qrCodePath = $qrCodeService->saveToStorage($qrData, $ticketNumber);
            
            // Atualiza a ficha com o caminho do QR code
            $ticket->update([
                'qr_code_path' => $qrCodePath,
                'status' => 'pendente',
            ]);
            
            // Gera PDF (que incluirá o QR code inline)
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateTicketPdf($ticket);
            
            // Atualiza a ficha com o caminho do PDF
            $ticket->update([
                'pdf_path' => $pdfPath,
                'status' => 'gerado',
                'generated_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Log do erro mas não falha a criação do pedido
            \Illuminate\Support\Facades\Log::error('Erro ao gerar ficha para pedido #'.$order->order_number.': '.$e->getMessage());
        }
    }
}
