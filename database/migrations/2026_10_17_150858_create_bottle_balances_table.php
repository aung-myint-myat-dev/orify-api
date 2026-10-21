<?php

use App\Models\Address;
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
        Schema::create('bottle_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Address::class)
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('balance') ->default(0);
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bottle_balances');
    }
};
