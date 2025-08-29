<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Administrativas
|--------------------------------------------------------------------------
| Todas as rotas aqui são protegidas pelo middleware 'admin' e requerem
| autenticação e perfil de administrador.
|
*/

Route::middleware(['auth', 'verified', 'user.active', 'admin'])->group(function () {
    // Gestão de Usuários
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('/users/{user}/change-role', [UserController::class, 'changeRole'])->name('users.change-role');
    
    // Gestão de Categorias
    Route::resource('categories', CategoryController::class);
    
    // Gestão de Produtos
    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    
    // Gestão de Estoque
    Route::resource('stock-movements', StockMovementController::class);
    Route::get('stock-movements/product/{product}/summary', [StockMovementController::class, 'productSummary'])->name('stock-movements.product-summary');
    
    // Gestão de Pedidos
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/advance-status', [OrderController::class, 'advanceStatus'])->name('orders.advance-status');
    Route::patch('orders/{order}/mark-as-delivered', [OrderController::class, 'markAsDelivered'])->name('orders.mark-as-delivered');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});
