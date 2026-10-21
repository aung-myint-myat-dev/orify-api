<?php

use App\Enums\PaymentMethodTypes;
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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            // method info
            $table->string('method_name', 100);
            $table->enum('method_type', PaymentMethodTypes::toArray());

            // receiver info
            $table->string('receiver_name', 150)->nullable();
            $table->string('receiver_phone_number', 30)->nullable();
            $table->string('receiver_account_number', 100)->nullable();
            $table->string('qr_code_path')->nullable();

            // admin only access
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
