<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders'     => Order::count(),
            'total_revenue'    => Order::where('payment_status', 'paid')->sum('total_price'),
            'today_revenue'    => Order::where('payment_status', 'paid')->whereDate('paid_at', today())->sum('total_price'),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'in_process'       => Order::whereIn('status', ['picked_up', 'weighing', 'washing', 'drying', 'ironing'])->count(),
            'ready_orders'     => Order::where('status', 'ready')->count(),
            'delivering'       => Order::where('status', 'delivering')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_customers'  => User::where('role', 'customer')->count(),
            'total_couriers'   => User::where('role', 'courier')->count(),
        ];

        $orders = Order::with(['customer', 'service', 'courier', 'address', 'statusLogs'])
            ->orderBy('created_at', 'desc')
            ->get();

        $services = Service::orderBy('id', 'asc')->get();
        $couriers = User::where('role', 'courier')->where('is_active', true)->get();
        $customers = User::where('role', 'customer')->get();

        return view('admin.dashboard', compact('stats', 'orders', 'services', 'couriers', 'customers'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $status = $request->status;
        $notes = $request->notes;

        if ($request->filled('total_weight')) {
            $order->total_weight = (float)$request->total_weight;
            $order->subtotal = $order->total_weight * $order->price_per_unit;
            $order->total_price = $order->subtotal + $order->delivery_fee;
        }

        if ($request->filled('payment_status')) {
            $order->payment_status = $request->payment_status;
            if ($request->payment_status === 'paid' && !$order->paid_at) {
                $order->paid_at = now();
            }
        }

        $admin = User::where('role', 'admin')->first();
        $order->transitionTo($status, $notes, $admin?->id);

        return redirect()->back()->with('success', "Status pesanan {$order->order_code} berhasil diubah ke {$order->status_label}!");
    }

    public function assignCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $courier = User::where('id', $request->courier_id)->where('role', 'courier')->firstOrFail();

        $order->update(['courier_id' => $courier->id]);

        $admin = User::where('role', 'admin')->first();
        $order->statusLogs()->create([
            'status'     => $order->status,
            'title'      => 'Penugasan Kurir',
            'notes'      => "Kurir {$courier->name} ({$courier->phone}) ditugaskan untuk pesanan ini.",
            'created_by' => $admin?->id,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', "Kurir {$courier->name} berhasil ditugaskan untuk {$order->order_code}.");
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'category'        => 'required|in:kiloan,satuan,dry_clean,express',
            'price_per_unit'  => 'required|numeric|min:0',
            'unit'            => 'required|string|max:20',
            'estimated_hours' => 'required|integer|min:1',
            'description'     => 'nullable|string',
        ]);

        Service::create([
            'name'            => $request->name,
            'slug'            => Str::slug($request->name) . '-' . Str::random(4),
            'category'        => $request->category,
            'price_per_unit'  => $request->price_per_unit,
            'unit'            => $request->unit,
            'estimated_hours' => $request->estimated_hours,
            'description'     => $request->description,
            'icon'            => 'local_laundry_service',
            'is_active'       => true,
        ]);

        return redirect()->back()->with('success', 'Layanan laundry baru berhasil ditambahkan.');
    }

    public function simulator()
    {
        $customer = User::where('role', 'customer')->with('addresses')->first();
        $services = Service::where('is_active', true)->get();
        $orders = Order::where('customer_id', $customer?->id)
            ->with(['service', 'statusLogs', 'courier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.simulator', compact('customer', 'services', 'orders'));
    }

    public function apiDocs()
    {
        $admin = User::where('role', 'admin')->first();
        $customer = User::where('role', 'customer')->first();

        $adminToken = $admin ? $admin->createToken('doc_token')->plainTextToken : 'Generate via login';
        $customerToken = $customer ? $customer->createToken('doc_token')->plainTextToken : 'Generate via login';

        return view('admin.api_docs', compact('adminToken', 'customerToken'));
    }
}

