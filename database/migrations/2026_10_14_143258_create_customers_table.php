<?php

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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->unique()
                ->constrained()
                ->restrictOnDelete();
            
            $table->string('customer_code', 20) ->unique();

            $table->string('phone_number_1', 30);
            $table->string('phone_number_2', 30) ->nullable();

            $table->boolean('is_active') ->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
