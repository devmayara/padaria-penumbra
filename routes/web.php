<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'user.active', 'redirect.members'])->name('dashboard');

Route::middleware(['auth', 'user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 
    // Geração de Fichas (Tickets) - Acessível por ambos os perfis
    Route::post('/tickets/generate/{order}', [TicketController::class, 'generate'])->name('tickets.generate');
});

// Incluir rotas específicas por perfil
require __DIR__.'/admin.php';
require __DIR__.'/member.php';
require __DIR__.'/auth.php';

// Rota catch-all para rotas inexistentes (404)
Route::fallback(function () {
    abort(404);
});
