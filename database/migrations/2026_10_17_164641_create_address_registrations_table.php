<?php

use App\Enums\AddressRegistarationStatus;
use App\Models\Address;
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
        Schema::create('address_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Address::class)
                ->constrained()
                ->restrictOnDelete();

            $table->string('registration_number', 150)->unique();

            $table->unsignedInteger('initial_bottle_quantity')->default(0);

            $table->enum(
                'status',
                AddressRegistarationStatus::toArray()
            )->default(AddressRegistarationStatus::PENDING->value);

            // Admin review information
            $table->foreignIdFor(User::class, 'reviewed_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_registrations');
    }
};
