<?php

use App\Enums\OrderStatus;
use App\Enums\OrderTypes;
use App\Models\Address;
use App\Models\Customer;
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

            $table->string('order_number', 50)->unique();

            $table->foreignIdFor(Customer::class)
                ->constrained()
                ->restrictOnDelete();

            $table->foreignIdFor(Address::class)
                ->constrained()
                ->restrictOnDelete();

            $table->enum('order_type', OrderTypes::toArray())->default(OrderTypes::REGULAR->value);

            // water bottle quantity. e.g; 1 water bottle, 2 water bottle, 3 water bottle, ...
            $table->unsignedInteger('water_bottle_quantity');

            // bottle with full of water e.g; 1800 mmk per 1 water bottle
            $table->decimal('water_bottle_unit_price', 12, 2);

            // water_bottle_quantity * water_bottle_unit_price.
            // e.g; 2 water bottle * 1800 mmk per water bottle = 3600 mmk
            $table->decimal('water_total_amount', 12, 2);


            // empty bottle quantity. e.g; 1 empty bottle, 2 empty bottle, ...
            $table->unsignedInteger('new_empty_bottle_quantity')->default(0);

            // empty bottle unit price. e.g; 2000 mmk per 1 empty bottle
            $table->decimal('empty_bottle_unit_price', 12, 2)->default(0);

            // new_empty_bottle_quantity * empty_bottle_unit_price.
            // e.g; 2 new empty bottle * 2000 mmk per empty bottle = 4000 mmk
            $table->decimal('new_empty_bottle_total_amount', 12, 2)->default(0);

            // Order summary
            $table->decimal('subtotal', 12, 2);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);

            // Order status
            $table->enum('order_status', OrderStatus::toArray())->default(OrderStatus::PENDING->value);

            // Delivery scheduling
            $table->date('expected_delivery_date')->nullable();

            // Delivery address snapshot
            $table->string('recipient_name', 150);
            $table->string('recipient_phone_number', 30);
            $table->string('state', 100);
            $table->string('city', 100);
            $table->string('township', 100);
            $table->string('ward', 100)->nullable();
            $table->string('street', 255)->nullable();
            $table->string('house_number', 50)->nullable();
            $table->string('building_number', 50)->nullable();
            $table->string('room_number', 50)->nullable();
            $table->string('floor_number', 50)->nullable();
            $table->string('landmark', 255)->nullable();
            $table->text('customer_note')->nullable();

            // delivery staff access
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('delivery_failure_reason')->nullable();

            // Soft hide, not deletion
            $table->timestamp('hidden_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['customer_id', 'created_at']);
            $table->index(['order_status', 'expected_delivery_date']);
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
