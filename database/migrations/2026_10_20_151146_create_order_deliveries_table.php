<?php

use App\Enums\OrderDeliveryStatus;
use App\Models\DeliveryTeam;
use App\Models\Order;
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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Order::class)
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignIdFor(DeliveryTeam::class, 'delivered_by')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedInteger('delivered_bottles_quantity')
                ->default(0);

            $table->enum('status', OrderDeliveryStatus::toArray())->default(OrderDeliveryStatus::PENDING->value);

            $table->timestamp('delivered_at')->nullable();

            $table->text('failure_reason')->nullable();
            $table->text('delivery_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
