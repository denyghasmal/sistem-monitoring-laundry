<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate payment simulation (QRIS, VA, Cash)
     * POST /api/v1/payments/pay
     */
    public function pay(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'payment_method' => 'required|string|in:cash,qris,bank_transfer,e_wallet',
            'instant_settle' => 'nullable|boolean',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah dibayar lunas.',
                'data'    => $order,
            ], 400);
        }

        $ref = 'PAY-' . strtoupper(Str::random(10));
        $method = $request->payment_method;

        $paymentInfo = [
            'order_code'        => $order->order_code,
            'amount'            => $order->total_price,
            'payment_method'    => $method,
            'payment_reference' => $ref,
            'status'            => 'pending',
            'instructions'      => '',
        ];

        if ($method === 'qris') {
            $paymentInfo['qr_string'] = '00020101021226600016ID.CO.QRIS.WWW01189360099900000000010215' . $order->order_code . '5802ID5303360540' . $order->total_price . '5802ID';
            $paymentInfo['instructions'] = 'Pindai kode QRIS menggunakan m-Banking atau E-Wallet pilihan Anda.';
        } elseif ($method === 'bank_transfer') {
            $paymentInfo['va_number'] = '8801' . str_pad($order->id, 8, '0', STR_PAD_LEFT);
            $paymentInfo['bank'] = 'BCA / Mandiri / BRI';
            $paymentInfo['instructions'] = 'Transfer tepat hingga digit terakhir ke Virtual Account di atas.';
        } else {
            $paymentInfo['instructions'] = 'Pembayaran tunai dapat diserahkan langsung ke kasir atau kurir saat serah terima.';
        }

        // If instant settle requested (e.g. for testing)
        if ($request->boolean('instant_settle') || $method === 'cash') {
            $order->update([
                'payment_status'    => ($method === 'cash' ? 'unpaid' : 'paid'),
                'payment_method'    => $method,
                'payment_reference' => $ref,
                'paid_at'           => ($method === 'cash' ? null : now()),
            ]);
            $paymentInfo['status'] = ($method === 'cash' ? 'waiting_cash' : 'paid');
        } else {
            $order->update([
                'payment_method'    => $method,
                'payment_reference' => $ref,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Instruksi pembayaran berhasil dibuat.',
            'data'    => $paymentInfo,
        ]);
    }

    /**
     * Webhook payment gateway callback
     * POST /api/v1/payments/callback
     */
    public function callback(Request $request)
    {
        Log::info('Payment Webhook Callback received: ', $request->all());

        $orderCode = $request->input('order_code') ?? $request->input('order_id');
        $transactionStatus = $request->input('transaction_status') ?? $request->input('status');

        if (!$orderCode) {
            return response()->json(['success' => false, 'message' => 'order_code parameter missing'], 400);
        }

        $order = Order::where('order_code', $orderCode)
            ->orWhere('id', $orderCode)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if (in_array($transactionStatus, ['settlement', 'capture', 'success', 'paid'])) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            $order->statusLogs()->create([
                'status'     => $order->status,
                'title'      => 'Pembayaran Lunas',
                'notes'      => 'Pembayaran sebesar Rp ' . number_format($order->total_price, 0, ',', '.') . ' telah diverifikasi otomatis oleh Payment Gateway.',
                'created_by' => null,
                'created_at' => now(),
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $order->update(['payment_status' => 'unpaid']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment callback processed successfully.',
            'data'    => [
                'order_code'     => $order->order_code,
                'payment_status' => $order->payment_status,
            ],
        ]);
    }
}

