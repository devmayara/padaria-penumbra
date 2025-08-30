<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        // Filtros de data
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // KPIs principais
        $totalUsers = User::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalOrders = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Pedidos por status
        $ordersByStatus = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Faturamento
        $totalRevenue = Order::where('status', 'pago')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_amount');

        $pendingRevenue = Order::where('status', 'pendente')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_amount');

        // Top clientes (mais pedidos pagos + somatório de valor)
        $topClients = User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'pago')
                  ->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        })
            ->withCount(['orders as total_orders' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'pago')
                      ->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            }])
            ->withSum(['orders as total_spent' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'pago')
                      ->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            }], 'total_amount')
            ->orderBy('total_spent', 'desc')
            ->orderBy('total_orders', 'desc')
            ->limit(10)
            ->get();

        // Estatísticas adicionais
        $recentOrders = Order::with(['user', 'items.product.category'])
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::with('category')
            ->where('current_quantity', '<=', 10)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeProducts',
            'totalOrders',
            'ordersByStatus',
            'totalRevenue',
            'pendingRevenue',
            'topClients',
            'recentOrders',
            'lowStockProducts',
            'startDate',
            'endDate'
        ));
    }
}
