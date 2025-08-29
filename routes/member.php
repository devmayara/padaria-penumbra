<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas para Membros
|--------------------------------------------------------------------------
| Rotas específicas para usuários com role 'member', incluindo:
| - Catálogo de produtos (marketplace)
| - Criação e gestão de pedidos próprios
| - Visualização de produtos
|
*/

Route::middleware(['auth', 'verified', 'user.active', 'member'])->group(function () {
    // Catálogo de Produtos (Marketplace) - Rotas específicas para membros
    Route::get('marketplace', [ProductController::class, 'marketplace'])->name('marketplace.index');
    Route::get('marketplace/{product}', [ProductController::class, 'marketplaceShow'])->name('marketplace.show');

    // Gestão de Pedidos (apenas pedidos próprios) - Rotas específicas para membros
    Route::get('my-orders', [OrderController::class, 'memberIndex'])->name('member.orders.index');
    Route::get('my-orders/create', [OrderController::class, 'memberCreate'])->name('member.orders.create');
    Route::post('my-orders', [OrderController::class, 'memberStore'])->name('member.orders.store');
    Route::get('my-orders/{order}', [OrderController::class, 'memberShow'])->name('member.orders.show');
    Route::patch('my-orders/{order}/cancel', [OrderController::class, 'memberCancel'])->name('member.orders.cancel');
});
