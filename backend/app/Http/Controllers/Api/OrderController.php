<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * List orders based on caller's role
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::with(['service', 'address', 'customer', 'courier'])
            ->orderBy('created_at', 'desc');

        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        } elseif ($user->isCourier()) {
            // Courier sees orders assigned to them, or pending orders available for pickup
            $query->where(function ($q) use ($user) {
                $q->where('courier_id', $user->id)
                  ->orWhere(function ($sub) {
                      $sub->whereNull('courier_id')
                          ->whereIn('status', ['pending', 'ready']);
                  });
            });
        }

        // Optional status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Daftar pesanan berhasil dimuat.',
            'data'    => $orders,
        ]);
    }

    /**
     * Customer creates a new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id'           => 'required|exists:services,id',
            'address_id'           => 'nullable|exists:addresses,id',
            'delivery_type'        => 'required|in:pickup_delivery,dropoff_pickup,dropoff_delivery',
            'estimated_weight'     => 'nullable|numeric|min:0.5',
            'unit_count'           => 'nullable|integer|min:1',
            'pickup_scheduled_at'  => 'nullable|date',
            'customer_notes'       => 'nullable|string',
            'payment_method'       => 'nullable|string|in:cash,qris,bank_transfer,e_wallet',
        ], [
            'service_id.required'    => 'Silakan pilih jenis layanan laundry.',
            'delivery_type.required' => 'Pilih metode antar-jemput pakaian.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi pesanan gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $service = Service::findOrFail($request->service_id);

        // Address check if delivery/pickup is needed
        $addressId = $request->address_id;
        if ($request->delivery_type !== 'dropoff_pickup' && !$addressId) {
            $primaryAddress = $user->addresses()->where('is_primary', true)->first();
            $addressId = $primaryAddress ? $primaryAddress->id : $user->addresses()->first()?->id;
        }

        // Generate unique order code: LND-YYYYMMDD-XXXX
        $todayDate = now()->format('Ymd');
        $randomSeq = strtoupper(Str::random(4));
        $orderCode = "LND-{$todayDate}-{$randomSeq}";

        // Calculate delivery fee
        $deliveryFee = ($request->delivery_type === 'pickup_delivery') ? 10000.0 : 0.0;
        $pricePerUnit = $service->price_per_unit;

        // Estimated total
        $initialWeight = $request->estimated_weight ?? ($service->unit === 'kg' ? 3.0 : 1.0);
        $subtotal = $initialWeight * $pricePerUnit;
        $totalPrice = $subtotal + $deliveryFee;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code'          => $orderCode,
                'customer_id'         => $user->id,
                'service_id'          => $service->id,
                'address_id'          => $addressId,
                'delivery_type'       => $request->delivery_type,
                'total_weight'        => $request->estimated_weight,
                'unit_count'          => $request->unit_count,
                'price_per_unit'      => $pricePerUnit,
                'subtotal'            => $subtotal,
                'delivery_fee'        => $deliveryFee,
                'total_price'         => $totalPrice,
                'status'              => 'pending',
                'payment_status'      => 'unpaid',
                'payment_method'      => $request->payment_method ?? 'cash',
                'pickup_scheduled_at' => $request->pickup_scheduled_at ? now()->parse($request->pickup_scheduled_at) : now()->addHours(1),
                'customer_notes'      => $request->customer_notes,
            ]);

            // Create initial status log
            $order->statusLogs()->create([
                'status'     => 'pending',
                'title'      => 'Pesanan Diterima',
                'notes'      => 'Pesanan telah dibuat dan menunggu penjemputan atau konfirmasi outlet.',
                'created_by' => $user->id,
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan laundry berhasil dibuat!',
                'data'    => $order->load(['service', 'address', 'statusLogs']),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order details
     */
    public function show(Request $request, $id)
    {
        $order = Order::with(['service', 'address', 'customer', 'courier', 'statusLogs.actor', 'review'])
            ->findOrFail($id);

        $user = $request->user();
        if ($user->isCustomer() && $order->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak melihat pesanan ini.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => $order,
        ]);
    }

    /**
     * Real-time monitoring & timeline tracking endpoint
     * As defined in Blueprint Section 6: GET /api/v1/orders/{id}/track
     */
    public function track(Request $request, $id)
    {
        $order = Order::with(['service', 'address', 'courier', 'statusLogs.actor'])
            ->where('id', $id)
            ->orWhere('order_code', $id)
            ->firstOrFail();

        $user = $request->user();
        if ($user && $user->isCustomer() && $order->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak melacak pesanan ini.',
            ], 403);
        }

        // Timeline milestones structure
        $allSteps = [
            ['key' => 'pending',    'title' => 'Pemesanan',       'desc' => 'Pesanan masuk dan dijadwalkan', 'step' => 1],
            ['key' => 'picked_up',  'title' => 'Penjemputan',     'desc' => 'Pakaian diambil kurir ke lokasi', 'step' => 2],
            ['key' => 'weighing',   'title' => 'Penimbangan',     'desc' => 'Penetapan bobot/pcs & tagihan', 'step' => 3],
            ['key' => 'washing',    'title' => 'Pencucian',       'desc' => 'Pakaian dicuci di mesin cuci', 'step' => 4],
            ['key' => 'drying',     'title' => 'Pengeringan',     'desc' => 'Proses pengeringan mesin', 'step' => 5],
            ['key' => 'ironing',    'title' => 'Penyetrikaan',    'desc' => 'Setrika uap & pewangi pakaian', 'step' => 6],
            ['key' => 'ready',      'title' => 'Siap Kirim/Ambil','desc' => 'Packing rapi dan siap didistribusikan', 'step' => 7],
            ['key' => 'delivering', 'title' => 'Pengantaran',     'desc' => 'Kurir mengantar kembali pakaian Anda', 'step' => 8],
            ['key' => 'completed',  'title' => 'Selesai',         'desc' => 'Pakaian telah diterima pelanggan', 'step' => 9],
        ];

        $currentStepNumber = $order->status_step;

        // Map status logs by status key
        $logMap = $order->statusLogs->keyBy('status');

        $timeline = array_map(function ($s) use ($currentStepNumber, $logMap) {
            $isCompleted = ($s['step'] <= $currentStepNumber);
            $isCurrent   = ($s['step'] === $currentStepNumber);
            $logEntry    = $logMap->get($s['key']);

            return [
                'step'         => $s['step'],
                'status_key'   => $s['key'],
                'title'        => $s['title'],
                'description'  => $logEntry ? $logEntry->notes : $s['desc'],
                'timestamp'    => $logEntry ? $logEntry->created_at->format('d M Y H:i') : null,
                'is_completed' => $isCompleted,
                'is_current'   => $isCurrent,
            ];
        }, $allSteps);

        return response()->json([
            'success' => true,
            'message' => 'Status pemantauan pesanan berhasil dimuat.',
            'data'    => [
                'order_code'     => $order->order_code,
                'current_status' => $order->status,
                'status_label'   => $order->status_label,
                'current_step'   => $currentStepNumber,
                'total_steps'    => count($allSteps),
                'total_price'    => $order->total_price,
                'payment_status' => $order->payment_status,
                'service_name'   => $order->service->name,
                'courier'        => $order->courier ? [
                    'name'   => $order->courier->name,
                    'phone'  => $order->courier->phone,
                    'avatar' => $order->courier->avatar,
                ] : null,
                'timeline'       => $timeline,
            ],
        ]);
    }

    /**
     * Update order status
     * PATCH /api/v1/orders/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status'       => 'required|in:pending,picked_up,weighing,washing,drying,ironing,ready,delivering,completed,cancelled',
            'notes'        => 'nullable|string',
            'total_weight' => 'nullable|numeric|min:0.1',
            'unit_count'   => 'nullable|integer|min:1',
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi perubahan status gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $newStatus = $request->status;

        // If status is weighing, calculate total price
        if ($newStatus === 'weighing' || $request->filled('total_weight') || $request->filled('unit_count')) {
            if ($request->filled('total_weight')) {
                $order->total_weight = $request->total_weight;
                $order->subtotal = $order->total_weight * $order->price_per_unit;
            } elseif ($request->filled('unit_count')) {
                $order->unit_count = $request->unit_count;
                $order->subtotal = $order->unit_count * $order->price_per_unit;
            }
            if ($request->filled('delivery_fee')) {
                $order->delivery_fee = $request->delivery_fee;
            }
            $order->total_price = $order->subtotal + $order->delivery_fee;
        }

        // If courier is updating to picked_up or delivering, auto-assign if null
        if ($user->isCourier() && !$order->courier_id) {
            $order->courier_id = $user->id;
        }

        // Transition status and create log
        $notes = $request->notes ?? Order::$statusTransitions[$newStatus]['desc'] ?? null;
        $order->transitionTo($newStatus, $notes, $user->id);

        // Simulation of FCM Push Notification
        if ($order->customer && $order->customer->fcm_token) {
            Log::info("FCM Notification sent to Customer {$order->customer->id}: Status is now {$newStatus}");
        }

        return response()->json([
            'success' => true,
            'message' => "Status pesanan {$order->order_code} berhasil diubah ke {$order->status_label}.",
            'data'    => $order->fresh()->load(['service', 'statusLogs']),
        ]);
    }

    /**
     * Admin assigns courier to order
     */
    public function assignCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'courier_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $courier = User::where('id', $request->courier_id)->where('role', 'courier')->firstOrFail();

        $order->update(['courier_id' => $courier->id]);

        $order->statusLogs()->create([
            'status'     => $order->status,
            'title'      => 'Penugasan Kurir',
            'notes'      => "Kurir {$courier->name} ({$courier->phone}) ditugaskan untuk pesanan ini.",
            'created_by' => $request->user()->id,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Kurir {$courier->name} berhasil ditugaskan.",
            'data'    => $order->fresh()->load(['courier', 'statusLogs']),
        ]);
    }

    /**
     * Customer adds review
     */
    public function addReview(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();

        if ($order->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
        }

        if ($order->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Pesanan belum selesai.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $review = Review::updateOrCreate(
            ['order_id' => $order->id],
            [
                'customer_id' => $user->id,
                'rating'      => $request->rating,
                'comment'     => $request->comment,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas ulasan dan penilaian Anda!',
            'data'    => $review,
        ]);
    }
}
