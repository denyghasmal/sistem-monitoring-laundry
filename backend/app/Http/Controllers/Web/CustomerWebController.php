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

class CustomerWebController extends Controller
{
    /**
     * Customer Portal Landing & Services Catalog
     */
    public function index()
    {
        $services = Service::where('is_active', true)->orderBy('id', 'asc')->get();
        $customer = User::where('role', 'customer')->first();
        $recentOrders = $customer ? Order::where('customer_id', $customer->id)->with('service')->latest()->take(3)->get() : collect();

        return view('customer.index', compact('services', 'customer', 'recentOrders'));
    }

    /**
     * Customer Order Creation Page
     */
    public function orderPage(Request $request)
    {
        $services = Service::where('is_active', true)->get();
        $selectedServiceId = $request->query('service_id', $services->first()?->id);
        $customer = User::where('role', 'customer')->with('addresses')->first();

        return view('customer.order', compact('services', 'selectedServiceId', 'customer'));
    }

    /**
     * Process Order Placement from Customer Web Form
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'service_id'       => 'required|exists:services,id',
            'delivery_type'    => 'required|in:pickup_delivery,dropoff_pickup',
            'estimated_weight' => 'required|numeric|min:0.5',
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => 'required|string|max:20',
            'pickup_address'   => 'required|string',
            'payment_method'   => 'required|in:cash,qris,bank_transfer',
            'customer_notes'   => 'nullable|string',
        ]);

        // Find or create customer
        $customer = User::firstOrCreate(
            ['phone' => $request->customer_phone],
            [
                'name'      => $request->customer_name,
                'email'     => 'user_' . Str::random(5) . '@laundry.local',
                'password'  => bcrypt('password'),
                'role'      => 'customer',
                'is_active' => true,
            ]
        );

        // Address
        $address = Address::firstOrCreate(
            [
                'user_id'      => $customer->id,
                'full_address' => $request->pickup_address,
            ],
            [
                'address_label'   => 'Alamat Jemput',
                'recipient_name'  => $customer->name,
                'recipient_phone' => $customer->phone,
                'is_primary'      => true,
            ]
        );

        $service = Service::findOrFail($request->service_id);
        $deliveryFee = ($request->delivery_type === 'pickup_delivery') ? 10000.0 : 0.0;
        $subtotal = (float)$request->estimated_weight * $service->price_per_unit;
        $totalPrice = $subtotal + $deliveryFee;

        $orderCode = 'LND-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code'          => $orderCode,
                'customer_id'         => $customer->id,
                'service_id'          => $service->id,
                'address_id'          => $address->id,
                'delivery_type'       => $request->delivery_type,
                'total_weight'        => $request->estimated_weight,
                'price_per_unit'      => $service->price_per_unit,
                'subtotal'            => $subtotal,
                'delivery_fee'        => $deliveryFee,
                'total_price'         => $totalPrice,
                'status'              => 'pending',
                'payment_status'      => 'unpaid',
                'payment_method'      => $request->payment_method,
                'pickup_scheduled_at' => now()->addHour(),
                'customer_notes'      => $request->customer_notes,
            ]);

            $order->statusLogs()->create([
                'status'     => 'pending',
                'title'      => 'Pesanan Masuk via Web',
                'notes'      => 'Pesanan berhasil dibuat melalui Web Portal Pelanggan.',
                'created_by' => $customer->id,
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('customer.track', ['code' => $order->order_code])
                ->with('success', "Pesanan {$order->order_code} berhasil dibuat! Anda sekarang dapat memantau status cucian di bawah ini.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Real-time Full-page Order Tracking Page
     */
    public function track(Request $request, $code = null)
    {
        $searchCode = $code ?? $request->query('code');
        $order = null;

        if ($searchCode) {
            $order = Order::with(['service', 'address', 'courier', 'statusLogs.actor'])
                ->where('order_code', $searchCode)
                ->orWhere('id', $searchCode)
                ->first();
        }

        // Default to first active order if not specified
        if (!$order) {
            $order = Order::with(['service', 'address', 'courier', 'statusLogs.actor'])
                ->latest()
                ->first();
        }

        $allOrders = Order::latest()->take(10)->get();

        return view('customer.track', compact('order', 'allOrders'));
    }

    /**
     * Customer Order History
     */
    public function history(Request $request)
    {
        $customer = User::where('role', 'customer')->first();
        $orders = Order::with(['service', 'address', 'courier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.history', compact('orders', 'customer'));
    }
}

