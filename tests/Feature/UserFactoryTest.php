<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_assigns_a_customer_role(): void
    {
        $user = User::factory()->create();

        $role = Role::findOrFail($user->role_id);

        $this->assertSame('customer', $role->name);
    }

    public function test_customer_factory_can_create_its_user(): void
    {
        $customer = Customer::factory()->create();

        $role = Role::findOrFail($customer->user->role_id);

        $this->assertSame('customer', $role->name);
    }

    public function test_role_seeder_persists_role_descriptions(): void
    {
        $this->seed(RoleSeeder::class);

        $this->assertNotEmpty(Role::where('name', 'customer')->firstOrFail()->description);
    }

    public function test_user_columns_migration_can_be_rolled_back(): void
    {
        $migration = require database_path('migrations/2026_10_13_120825_add_columns_to_users_table.php');
        $migration->down();

        $this->assertFalse(Schema::hasColumn('users', 'role_id'));
        $this->assertFalse(Schema::hasColumn('users', 'is_active'));
    }

    public function test_delivery_team_members_has_the_expected_unique_index(): void
    {
        $indexes = Schema::getIndexes('delivery_team_members');

        $this->assertContains(
            ['delivery_team_id', 'employee_id'],
            array_column($indexes, 'columns')
        );
    }
}
