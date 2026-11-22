<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kiloan Reguler, Kilat Express, Bed Cover, Dry Clean
            $table->string('slug')->unique();
            $table->enum('category', ['kiloan', 'satuan', 'dry_clean', 'express'])->default('kiloan');
            $table->text('description')->nullable();
            $table->decimal('price_per_unit', 12, 2);
            $table->string('unit', 20)->default('kg'); // kg, pcs, meter, set
            $table->integer('estimated_hours')->default(24);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

