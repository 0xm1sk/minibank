<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;


class TestClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use firstOrCreate to avoid duplicate entries
        $client = User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role_id' => 1, // client
            ]
        );

        // Create an account for the client if it doesn't exist
        $account = Account::firstOrCreate(
            ['user_id' => $client->id],
            [
                'account_number' => 'ACC123456',
                'balance' => 1000,
            ]
        );

        // Add transactions only if they don't exist
        Transaction::firstOrCreate(
            [
                'account_id' => $account->id,
                'description' => 'Initial deposit'
            ],
            [
                'type' => 'credit',
                'amount' => 1000,
            ]
        );

        Transaction::firstOrCreate(
            [
                'account_id' => $account->id,
                'description' => 'Grocery shopping'
            ],
            [
                'type' => 'debit',
                'amount' => 150,
            ]
        );
    }    
}
