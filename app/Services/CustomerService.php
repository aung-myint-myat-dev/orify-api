<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;

class CustomerService
{
    public function createForUser(User $user): Customer
    {
        $lastCustomerId = Customer::withTrashed()
            ->orderByDesc('id')
            ->lockForUpdate()
            ->value('id') ?? 0;
        $customerNumber = (int) $lastCustomerId + 1;

        do {
            $customerCode = 'CUS-'.str_pad((string) $customerNumber, 5, '0', STR_PAD_LEFT);
            $customerNumber++;
        } while (Customer::withTrashed()->where('customer_code', $customerCode)->exists());

        return $user->customer()->create([
            'customer_code' => $customerCode,
            'phone_number_1' => null,
        ]);
    }
}
