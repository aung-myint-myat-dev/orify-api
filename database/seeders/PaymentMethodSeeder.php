<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodTypes;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payment_methods = [
            [
                'method_name' => 'Kpay',
                'method_type' => PaymentMethodTypes::MOBILE_WALLET->value,
                'receiver_name' => 'Ko Hla Tun',
                'receiver_phone_number' => '09988987677',
            ],
            [
                'method_name' => 'Wave Money',
                'method_type' => PaymentMethodTypes::MOBILE_WALLET->value,
                'receiver_name' => 'Ko Hla Tun',
                'receiver_phone_number' => '09988987677',
            ],
            [
                'method_name' => 'KBZ',
                'method_type' => PaymentMethodTypes::BANKING->value,
                'receiver_name' => 'Ko Hla Tun',
                'receiver_account_number' => '1118989998987',
            ],
        ];

        foreach ($payment_methods as $method) {
            PaymentMethod::firstOrCreate(
                ['method_name' => $method['method_name']],
                [
                    'method_type' => $method['method_type'],
                    'receiver_name' => $method['receiver_name'],
                    'receiver_phone_number' => $method['receiver_phone_number'] ?? null,
                    'receiver_account_number' => $method['receiver_account_number'] ?? null,
                ]
            );
        }
    }
}
