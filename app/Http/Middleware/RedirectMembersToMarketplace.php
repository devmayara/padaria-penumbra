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
        if (Auth::check()) {
            $user = Auth::user();
            
            // Se for membro e estiver tentando acessar o dashboard, redireciona para o marketplace
            if ($user->role === 'member' && $request->routeIs('dashboard')) {
                return redirect()->route('marketplace.index');
            }
            
            // Se for admin e estiver tentando acessar o dashboard, redireciona para o admin dashboard
            if ($user->role === 'admin' && $request->routeIs('dashboard')) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
