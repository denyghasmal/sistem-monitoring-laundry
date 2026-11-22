<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * List couriers (for assignment dropdown)
     */
    public function index()
    {
        $couriers = User::where('role', 'courier')
            ->where('is_active', true)
            ->select('id', 'name', 'phone', 'avatar')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $couriers,
        ]);
    }

    /**
     * Courier active task summary
     */
    public function tasks(Request $request)
    {
        $courier = $request->user();

        $pickups = Order::with(['customer', 'address', 'service'])
            ->where('status', 'pending')
            ->where(function ($q) use ($courier) {
                $q->where('courier_id', $courier->id)
                  ->orWhereNull('courier_id');
            })
            ->get();

        $deliveries = Order::with(['customer', 'address', 'service'])
            ->whereIn('status', ['ready', 'delivering'])
            ->where('courier_id', $courier->id)
            ->get();

        $completedToday = Order::where('courier_id', $courier->id)
            ->where('status', 'completed')
            ->whereDate('delivered_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'pickups_available' => $pickups,
                'deliveries_active' => $deliveries,
                'completed_today'   => $completedToday,
            ],
        ]);
    }
}

