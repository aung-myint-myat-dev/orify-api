<?php

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Order::class)
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignIdFor(PaymentMethod::class)
                ->constrained()
                ->restrictOnDelete();

            $table->string('payment_number', 50)->unique();

            $table->decimal('amount', 12, 2);

            $table->string('transaction_id', 150)->nullable();
            $table->string('transaction_screenshot_path', 255)->nullable();

            // Admin verification
            $table->enum('status', PaymentStatus::toArray())
                ->default(PaymentStatus::PENDING->value);

            $table->text('rejection_reason')->nullable();

            $table->foreignIdFor(User::class, 'verified_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
