<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class RoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create Employee
        $employee = User::firstOrCreate(
            ['email' => 'employee@test.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'role_id' => 2, // Employee
            ]
        );

        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => 3, // Admin
            ]
        );

        // Create another client for testing
        $client2 = User::firstOrCreate(
            ['email' => 'client2@test.com'],
            [
                'name' => 'Alice Johnson',
                'password' => Hash::make('password'),
                'role_id' => 1, // Client
            ]
        );

        // Create account for client2 if it doesn't exist
        if ($client2) {
            Account::firstOrCreate(
                ['user_id' => $client2->id],
                [
                    'account_number' => 'ACC' . str_pad($client2->id, 7, '0', STR_PAD_LEFT),
                    'balance' => 5000,
                ]
            );
        }

        $this->command->info(string: 'Role-based users created successfully!');
    }
}