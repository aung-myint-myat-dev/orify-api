<?php

use App\Models\Address;
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
        Schema::create('bottle_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Address::class)
                ->constrained()
                ->restrictOnDelete();

            $table->foreignIdFor(Order::class)
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedInteger('bottle_balance_before');
            $table->unsignedInteger('bottle_balance_after');

            $table->text('remark')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['address_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bottle_transactions');
    }
};
