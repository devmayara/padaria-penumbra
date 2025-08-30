<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'user.active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'member' => \App\Http\Middleware\EnsureUserIsMember::class,
            'redirect.members' => \App\Http\Middleware\RedirectMembersToMarketplace::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tratamento de exceções de autenticação
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            return redirect()->route('login');
        });

        // Tratamento de exceções de autorização (403)
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [], 403);
            }
        });
    })->create();
