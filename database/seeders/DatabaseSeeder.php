<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            EmployeePositionSeeder::class,
            PaymentMethodSeeder::class,
            DeliveryTeamSeeder::class,
            SettingSeeder::class,
        ]);

        // === ROLES IDS === //
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $customerRoleId = Role::where('name', 'customer')->value('id');
        $employeeRoleId = Role::where('name', 'employee')->value('id');

        // === POSITIONS IDS === //
        $driverPositionId = EmployeePosition::where('name', 'Driver')->value('id');
        $deliveryStaffPositionId = EmployeePosition::where('name', 'Delivery Staff')->value('id');

        // === ADMIN SEEDER === //
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role_id' => $adminRoleId,
        ]);

        // === CUSTOMERS SEEDER === //
        $customers = User::factory()
            ->count(40)
            ->has(Customer::factory())
            ->create([
                'role_id' => $customerRoleId,
            ]);

        // === DRIVER SEEDER === //
        $drivers = User::factory()
            ->count(5)
            ->has(Employee::factory()->state([
                'employee_position_id' => $driverPositionId,
            ]))
            ->create([
                'role_id' => $employeeRoleId,
            ]);

        // === DELIVERY STAFF SEEDER === //
        $deliveryStaff = User::factory()
            ->count(5)
            ->has(Employee::factory()->state([
                'employee_position_id' => $deliveryStaffPositionId,
            ]))
            ->create([
                'role_id' => $employeeRoleId,
            ]);

        $this->call(DeliveryTeamMemberSeeder::class);

    }
}
