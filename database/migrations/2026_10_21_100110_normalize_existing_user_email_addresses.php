<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['users', 'password_reset_tokens'] as $table) {
            $hasCanonicalDuplicates = DB::table($table)
                ->selectRaw('LOWER(TRIM(email))')
                ->groupByRaw('LOWER(TRIM(email))')
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($hasCanonicalDuplicates) {
                throw new RuntimeException(
                    "Cannot normalize {$table} emails while case-insensitive duplicate addresses exist."
                );
            }
        }

        DB::table('users')->update([
            'email' => DB::raw('LOWER(TRIM(email))'),
        ]);

        DB::table('password_reset_tokens')->update([
            'email' => DB::raw('LOWER(TRIM(email))'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Email normalization is intentionally irreversible.
    }
};
