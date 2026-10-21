<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_number_1', 30)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('customers')->whereNull('phone_number_1')->exists()) {
            throw new LogicException(
                'Cannot make customers.phone_number_1 required while customer records contain null values.'
            );
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_number_1', 30)->nullable(false)->change();
        });
    }
};
