<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->string('payer_name');
            $table->string('status', 32)->default('pending');
            $table->string('delivery_type', 32); // local_pickup, shipping
            $table->json('shipping_address')->nullable();
            $table->foreignId('pickup_location_id')->nullable()->constrained('marketplace_pickup_locations')->nullOnDelete();
            $table->string('tracking_code')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_orders');
    }
};
