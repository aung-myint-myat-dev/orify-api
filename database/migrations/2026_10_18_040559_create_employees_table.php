<?php

use App\Models\EmployeePosition;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignIdFor(EmployeePosition::class)
                ->constrained()
                ->restrictOnDelete();

            $table->string('employee_code', 50)->unique();
            $table->string('phone_number', 30)->nullable();
            $table->date('hired_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
