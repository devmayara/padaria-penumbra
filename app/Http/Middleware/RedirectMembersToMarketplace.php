<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectMembersToMarketplace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário estiver logado e for membro, redireciona para o marketplace
        if (Auth::check() && Auth::user()->role === 'member') {
            // Se estiver tentando acessar o dashboard, redireciona para o marketplace
            if ($request->routeIs('dashboard')) {
                return redirect()->route('marketplace.index');
            }
        }

        return $next($request);
    }
}
