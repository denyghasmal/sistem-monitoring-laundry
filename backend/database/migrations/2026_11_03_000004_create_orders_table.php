<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 32)->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            
            $table->enum('delivery_type', ['pickup_delivery', 'dropoff_pickup', 'dropoff_delivery'])->default('pickup_delivery');
            
            $table->decimal('total_weight', 8, 2)->nullable();
            $table->integer('unit_count')->nullable();
            $table->decimal('price_per_unit', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);

            $table->enum('status', [
                'pending',
                'picked_up',
                'weighing',
                'washing',
                'drying',
                'ironing',
                'ready',
                'delivering',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->string('payment_method')->default('cash'); // cash, qris, bank_transfer, e_wallet
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

