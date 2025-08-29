<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stockMovements']);

        // Busca por nome
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filtro por categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->orderBy('name')->paginate(5);
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        
        // Gera o slug automaticamente se não fornecido
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        } else {
            // Garante que o slug esteja formatado corretamente
            $data['slug'] = Str::slug($data['slug']);
        }

        // Processa upload de imagem
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->uploadImage($request->file('image'));
        }

        // Define valores padrão
        $data['is_active'] = true; // Sempre ativo por padrão

        $product = Product::create($data);

        // Registra movimentação inicial de estoque se houver quantidade
        if ($data['current_quantity'] > 0) {
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'entrada',
                'quantity' => $data['current_quantity'],
                'unit_price' => $data['unit_price'],
                'reason' => 'Criação inicial do produto',
                'notes' => 'Estoque inicial definido na criação do produto',
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements.user']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        // Gera o slug automaticamente se não fornecido
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);
        } else {
            // Garante que o slug esteja formatado corretamente
            $data['slug'] = Str::slug($data['slug']);
        }

        // Processa upload de nova imagem
        if ($request->hasFile('image')) {
            // Remove imagem antiga
            if ($product->image_path) {
                $this->deleteImage($product->image_path);
            }
            $data['image_path'] = $this->uploadImage($request->file('image'));
        }

        // Define valores padrão
        $data['is_active'] = $request->has('is_active');

        // Verifica se houve alteração no estoque
        $oldQuantity = $product->current_quantity;
        $newQuantity = $data['current_quantity'];
        $quantityChanged = $oldQuantity != $newQuantity;

        $product->update($data);

        // Registra movimentação se o estoque foi alterado
        if ($quantityChanged) {
            $difference = $newQuantity - $oldQuantity;
            $movementType = 'ajuste';
            
            if ($difference > 0) {
                $movementType = 'entrada';
            } elseif ($difference < 0) {
                $movementType = 'saida';
            }

            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $movementType,
                'quantity' => abs($difference),
                'unit_price' => $product->unit_price,
                'reason' => 'Edição do produto',
                'notes' => "Estoque alterado de {$oldQuantity} para {$newQuantity} via edição do produto",
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Remove imagem se existir
        if ($product->image_path) {
            $this->deleteImage($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'ativado' : 'desativado';
        return redirect()->route('products.index')
            ->with('success', "Produto {$status} com sucesso!");
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'current_quantity' => 'required|integer|min:0',
        ], [
            'current_quantity.required' => 'A quantidade é obrigatória.',
            'current_quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'current_quantity.min' => 'A quantidade não pode ser negativa.',
        ]);

        $oldQuantity = $product->current_quantity;
        $newQuantity = $request->current_quantity;
        $difference = $newQuantity - $oldQuantity;

        // Determina o tipo de movimentação
        $movementType = 'ajuste';
        if ($difference > 0) {
            $movementType = 'entrada';
        } elseif ($difference < 0) {
            $movementType = 'saida';
        }

        // Atualiza o estoque do produto
        $product->update(['current_quantity' => $newQuantity]);

        // Registra a movimentação se houve alteração
        if ($difference != 0) {
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $movementType,
                'quantity' => abs($difference),
                'unit_price' => $product->unit_price,
                'reason' => 'Atualização manual de estoque',
                'notes' => "Estoque alterado de {$oldQuantity} para {$newQuantity} via interface de produto",
            ]);
        }

        return redirect()->route('products.show', $product)
            ->with('success', 'Estoque atualizado com sucesso!');
    }

    /**
     * Generate a unique slug for the product
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists
     */
    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Product::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Upload and process image
     */
    private function uploadImage($image)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('products', $filename, 'public');

        return $path;
    }

    /**
     * Delete image
     */
    private function deleteImage($imagePath)
    {
        // Remove imagem principal
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
