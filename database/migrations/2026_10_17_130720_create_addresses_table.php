<?php

use App\Enums\AddressVerificationStatus;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Customer::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('label', 50);

            $table->string('recipient_name', 150);
            $table->string('recipient_phone_number', 30);

            $table->string('state', 100);
            $table->string('city', 100);
            $table->string('township', 100);
            $table->string('ward', 100);
            $table->string('street', 100);

            $table->string('house_number', 100)->nullable();
            $table->string('building_number', 100)->nullable();
            $table->string('room_number', 100)->nullable();
            $table->string('floor_number', 100)->nullable();
            $table->string('landmark', 100)->nullable();
            $table->text('remark')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default')->default(false);

            // Admin only access
            $table->enum(
                'verification_status',
                AddressVerificationStatus::toArray()
            )->default(AddressVerificationStatus::PENDING->value);

            $table->text('verification_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
