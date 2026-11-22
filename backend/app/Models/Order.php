<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'courier_id',
        'service_id',
        'address_id',
        'delivery_type',
        'total_weight',
        'unit_count',
        'price_per_unit',
        'subtotal',
        'delivery_fee',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'pickup_scheduled_at',
        'delivered_at',
        'customer_notes',
        'admin_notes',
    ];

    protected $casts = [
        'total_weight' => 'float',
        'unit_count' => 'integer',
        'price_per_unit' => 'float',
        'subtotal' => 'float',
        'delivery_fee' => 'float',
        'total_price' => 'float',
        'paid_at' => 'datetime',
        'pickup_scheduled_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'status_step',
    ];

    public static array $statusTransitions = [
        'pending'    => ['label' => 'Menunggu Konfirmasi', 'step' => 1, 'desc' => 'Pesanan berhasil dibuat, menunggu konfirmasi outlet/kurir.'],
        'picked_up'  => ['label' => 'Pakaian Dijemput', 'step' => 2, 'desc' => 'Pakaian telah diambil kurir dan dibawa menuju gerai.'],
        'weighing'   => ['label' => 'Penimbangan & Tagihan', 'step' => 3, 'desc' => 'Pakaian sedang ditimbang di outlet dan rincian harga ditetapkan.'],
        'washing'    => ['label' => 'Sedang Dicuci', 'step' => 4, 'desc' => 'Pakaian sedang dalam proses pencucian dengan deterjen khusus.'],
        'drying'     => ['label' => 'Sedang Dikeringkan', 'step' => 5, 'desc' => 'Pakaian sedang dikeringkan menggunakan mesin pengering suhu terkontrol.'],
        'ironing'    => ['label' => 'Sedang Disetrika', 'step' => 6, 'desc' => 'Pakaian sedang disetrika uap dan diberi pewangi.'],
        'ready'      => ['label' => 'Siap Diantar / Diambil', 'step' => 7, 'desc' => 'Pakaian sudah rapi, dipacking higienis, dan siap serah terima.'],
        'delivering' => ['label' => 'Sedang Diantar Kurir', 'step' => 8, 'desc' => 'Kurir sedang dalam perjalanan menuju alamat pelanggan.'],
        'completed'  => ['label' => 'Selesai', 'step' => 9, 'desc' => 'Pakaian telah diterima pelanggan dengan baik. Transaksi selesai.'],
        'cancelled'  => ['label' => 'Dibatalkan', 'step' => 0, 'desc' => 'Pesanan dibatalkan.'],
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'asc');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusTransitions[$this->status]['label'] ?? $this->status;
    }

    public function getStatusStepAttribute(): int
    {
        return self::$statusTransitions[$this->status]['step'] ?? 1;
    }

    /**
     * Helper to transition to a new status and create a log entry
     */
    public function transitionTo(string $newStatus, ?string $notes = null, ?int $userId = null): void
    {
        $this->status = $newStatus;
        if ($newStatus === 'completed') {
            $this->delivered_at = now();
        }
        $this->save();

        $info = self::$statusTransitions[$newStatus] ?? [
            'label' => ucfirst($newStatus),
            'desc' => $notes ?? 'Status diubah menjadi ' . $newStatus,
        ];

        $this->statusLogs()->create([
            'status' => $newStatus,
            'title' => $info['label'],
            'notes' => $notes ?? $info['desc'],
            'created_by' => $userId,
            'created_at' => now(),
        ]);
    }
}

