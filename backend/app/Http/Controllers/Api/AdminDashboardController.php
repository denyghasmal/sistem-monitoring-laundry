<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function stats()
    {
        $totalOrders = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalCouriers = User::where('role', 'courier')->count();

        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('total_price');

        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_price');

        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total_price');

        $pendingCount = Order::where('status', 'pending')->count();
        $inProcessCount = Order::whereIn('status', ['picked_up', 'weighing', 'washing', 'drying', 'ironing'])->count();
        $readyCount = Order::where('status', 'ready')->count();
        $deliveringCount = Order::where('status', 'delivering')->count();
        $completedCount = Order::where('status', 'completed')->count();

        $statusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentOrders = Order::with(['customer', 'service', 'courier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'total_orders'     => $totalOrders,
                    'total_customers'  => $totalCustomers,
                    'total_couriers'   => $totalCouriers,
                    'today_revenue'    => $todayRevenue,
                    'monthly_revenue'  => $monthlyRevenue,
                    'total_revenue'    => $totalRevenue,
                    'pending_orders'   => $pendingCount,
                    'in_process_orders'=> $inProcessCount,
                    'ready_orders'     => $readyCount,
                    'delivering_orders'=> $deliveringCount,
                    'completed_orders' => $completedCount,
                ],
                'status_breakdown' => $statusBreakdown,
                'recent_orders'    => $recentOrders,
            ],
        ]);
    }

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $orders = Order::with(['customer', 'service', 'courier'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $revenueByService = Order::where('payment_status', 'paid')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('SUM(orders.total_price) as total_revenue'), DB::raw('COUNT(orders.id) as total_orders'))
            ->groupBy('services.name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'period'             => ['start' => $startDate, 'end' => $endDate],
                'orders'             => $orders,
                'revenue_by_service' => $revenueByService,
                'total_amount'       => $orders->where('payment_status', 'paid')->sum('total_price'),
            ],
        ]);
    }
}

