<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Http\Requests\StockMovementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product.category', 'user']);

        // Filtro por produto
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por data
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stockMovements = $query->orderBy('created_at', 'desc')->paginate(10);
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact('stockMovements', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('stock-movements.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockMovementRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $stockMovement = StockMovement::create([
                    'product_id' => $request->product_id,
                    'user_id' => Auth::id(),
                    'type' => $request->type,
                    'quantity' => $request->quantity,
                    'unit_price' => $request->unit_price,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                ]);

                // Atualiza o estoque do produto
                $product = Product::findOrFail($request->product_id);
                $currentQuantity = $product->current_quantity;

                if ($request->type === 'entrada') {
                    $product->update(['current_quantity' => $currentQuantity + $request->quantity]);
                } elseif ($request->type === 'saida') {
                    if ($currentQuantity < $request->quantity) {
                        throw new \Exception('Estoque insuficiente para esta saída.');
                    }
                    $product->update(['current_quantity' => $currentQuantity - $request->quantity]);
                } elseif ($request->type === 'ajuste') {
                    $product->update(['current_quantity' => $request->quantity]);
                }
            });

            return redirect()->route('stock-movements.index')
                ->with('success', 'Movimentação de estoque registrada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao registrar movimentação: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['product.category', 'user']);
        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovement $stockMovement)
    {
        $stockMovement->load(['product.category', 'user']);
        $products = Product::orderBy('name')->get();
        return view('stock-movements.edit', compact('stockMovement', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockMovementRequest $request, StockMovement $stockMovement)
    {
        try {
            DB::transaction(function () use ($request, $stockMovement) {
                $oldQuantity = $stockMovement->quantity;
                $oldType = $stockMovement->type;
                $product = $stockMovement->product;
                $currentQuantity = $product->current_quantity;

                // Reverte a movimentação anterior
                if ($oldType === 'entrada') {
                    $product->update(['current_quantity' => $currentQuantity - $oldQuantity]);
                } elseif ($oldType === 'saida') {
                    $product->update(['current_quantity' => $currentQuantity + $oldQuantity]);
                } elseif ($oldType === 'ajuste') {
                    // Para ajustes, precisamos saber o valor anterior
                    // Como não temos essa informação, vamos usar a quantidade atual
                    $product->update(['current_quantity' => $oldQuantity]);
                }

                // Aplica a nova movimentação
                if ($request->type === 'entrada') {
                    $product->update(['current_quantity' => $product->current_quantity + $request->quantity]);
                } elseif ($request->type === 'saida') {
                    if ($product->current_quantity < $request->quantity) {
                        throw new \Exception('Estoque insuficiente para esta saída.');
                    }
                    $product->update(['current_quantity' => $product->current_quantity - $request->quantity]);
                } elseif ($request->type === 'ajuste') {
                    $product->update(['current_quantity' => $request->quantity]);
                }

                // Atualiza a movimentação
                $stockMovement->update([
                    'type' => $request->type,
                    'quantity' => $request->quantity,
                    'unit_price' => $request->unit_price,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                ]);
            });

            return redirect()->route('stock-movements.show', $stockMovement)
                ->with('success', 'Movimentação de estoque atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar movimentação: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        try {
            DB::transaction(function () use ($stockMovement) {
                $product = $stockMovement->product;
                $currentQuantity = $product->current_quantity;

                // Reverte a movimentação
                if ($stockMovement->type === 'entrada') {
                    $product->update(['current_quantity' => $currentQuantity - $stockMovement->quantity]);
                } elseif ($stockMovement->type === 'saida') {
                    $product->update(['current_quantity' => $currentQuantity + $stockMovement->quantity]);
                } elseif ($stockMovement->type === 'ajuste') {
                    // Para ajustes, não podemos reverter sem saber o valor anterior
                    // Vamos apenas remover a movimentação
                }

                $stockMovement->delete();
            });

            return redirect()->route('stock-movements.index')
                ->with('success', 'Movimentação de estoque excluída com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir movimentação: ' . $e->getMessage());
        }
    }

    /**
     * Show stock movements for a specific product.
     */
    public function productSummary(Product $product)
    {
        $stockMovements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stock-movements.product-summary', compact('product', 'stockMovements'));
    }
}
