<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
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

        // Relatório de pedidos
        $ordersReport = $this->getOrdersReport($startDate, $endDate);

        // Relatório de itens mais vendidos
        $topSellingItems = $this->getTopSellingItems($startDate, $endDate);

        // Relatório de top clientes
        $topClients = $this->getTopClients($startDate, $endDate);

        return view('admin.reports.index', compact(
            'ordersReport',
            'topSellingItems',
            'topClients',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export orders report to CSV.
     */
    public function exportOrdersCsv(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $orders = $this->getOrdersForExport($startDate, $endDate);

        $filename = 'relatorio_pedidos_'.$startDate->format('Y-m-d').'_'.$endDate->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Cabeçalho
            fputcsv($file, [
                'Cliente', 'Status', 'Total',
                'Data do Pedido', 'Data de Entrega', 'Data de Cancelamento',
            ]);

            // Dados
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->user->name,
                    $order->status,
                    number_format($order->total_amount, 2, ',', '.'),
                    $order->created_at ? Carbon::parse($order->created_at)->format('d/m/Y H:i') : '',
                    $order->delivered_at ? Carbon::parse($order->delivered_at)->format('d/m/Y H:i') : '',
                    $order->cancelled_at ? Carbon::parse($order->cancelled_at)->format('d/m/Y H:i') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export orders report to PDF.
     */
    public function exportOrdersPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $orders = $this->getOrdersForExport($startDate, $endDate);
        $ordersByStatus = $this->getOrdersByStatus($startDate, $endDate);
        $totalRevenue = $this->getTotalRevenue($startDate, $endDate);

        $data = [
            'orders' => $orders,
            'ordersByStatus' => $ordersByStatus,
            'totalRevenue' => $totalRevenue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => Carbon::now(),
        ];

        $pdf = Pdf::loadView('admin.reports.orders-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'relatorio_pedidos_'.$startDate->format('Y-m-d').'_'.$endDate->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export top selling items to CSV.
     */
    public function exportTopSellingCsv(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $topSellingItems = $this->getTopSellingItems($startDate, $endDate);

        $filename = 'relatorio_itens_mais_vendidos_'.$startDate->format('Y-m-d').'_'.$endDate->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($topSellingItems) {
            $file = fopen('php://output', 'w');

            // Cabeçalho
            fputcsv($file, [
                'Produto', 'Categoria', 'Quantidade Vendida', 'Total Faturado', 'Preço Unitário',
            ]);

            // Dados
            foreach ($topSellingItems as $item) {
                fputcsv($file, [
                    $item->product_name,
                    $item->category_name,
                    $item->total_quantity,
                    'R$ '.number_format($item->total_revenue, 2, ',', '.'),
                    'R$ '.number_format($item->unit_price, 2, ',', '.'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export top clients to CSV.
     */
    public function exportTopClientsCsv(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $topClients = $this->getTopClients($startDate, $endDate);

        $filename = 'relatorio_top_clientes_'.$startDate->format('Y-m-d').'_'.$endDate->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($topClients) {
            $file = fopen('php://output', 'w');

            // Cabeçalho
            fputcsv($file, [
                'Cliente', 'E-mail', 'Total de Pedidos', 'Total Gasto', 'Média por Pedido',
            ]);

            // Dados
            foreach ($topClients as $client) {
                $averageOrder = $client->total_spent > 0 ? $client->total_spent / $client->total_orders : 0;
                fputcsv($file, [
                    $client->name,
                    $client->email,
                    $client->total_orders,
                    'R$ '.number_format($client->total_spent, 2, ',', '.'),
                    'R$ '.number_format($averageOrder, 2, ',', '.'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get orders report data.
     */
    private function getOrdersReport($startDate, $endDate)
    {
        $orders = Order::with(['user', 'items.product.category'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersByStatus = $this->getOrdersByStatus($startDate, $endDate);
        $totalRevenue = $this->getTotalRevenue($startDate, $endDate);

        return [
            'orders' => $orders,
            'ordersByStatus' => $ordersByStatus,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $orders->count(),
        ];
    }

    /**
     * Get top selling items.
     */
    private function getTopSellingItems($startDate, $endDate)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('orders.status', ['pendente', 'pago', 'entregue'])
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->select(
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue'),
                'order_items.unit_price'
            )
            ->groupBy('products.id', 'products.name', 'categories.name', 'order_items.unit_price')
            ->orderBy('total_quantity', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get top clients.
     */
    private function getTopClients($startDate, $endDate)
    {
        return User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->whereIn('status', ['pendente', 'pago', 'entregue'])
                  ->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        })
            ->withCount(['orders as total_orders' => function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['pendente', 'pago', 'entregue'])
                      ->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            }])
            ->withSum(['orders as total_spent' => function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['pendente', 'pago', 'entregue'])
                      ->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            }], 'total_amount')
            ->orderBy('total_spent', 'desc')
            ->orderBy('total_orders', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get orders for export.
     */
    private function getOrdersForExport($startDate, $endDate)
    {
        return Order::with(['user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get orders by status.
     */
    private function getOrdersByStatus($startDate, $endDate)
    {
        return Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Get total revenue.
     */
    private function getTotalRevenue($startDate, $endDate)
    {
        return Order::whereIn('status', ['pendente', 'pago', 'entregue'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_amount');
    }
}
