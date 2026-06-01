<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name')->nullable(); // for walk-in / manual orders
            $table->string('vendor_name');
            $table->string('delivery_location');
            $table->date('delivery_date');
            $table->string('time_slot');
            $table->json('items');
            $table->decimal('subtotal', 8, 2);
            $table->decimal('delivery_fee', 8, 2)->default(2.00);
            $table->decimal('total', 8, 2);
            $table->text('notes')->nullable();
            $table->text('vendor_note')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
