<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Database Seeder - Creates test data for Mini Bank
 *
 * This seeder creates:
 * - 3 test users (Client, Employee, Admin)
 * - Bank accounts for each user
 * - Some sample transactions
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with different roles
        $this->createTestUsers();

        // Create additional sample clients
        $this->createSampleClients();

        // Create some sample transactions
        $this->createSampleTransactions();

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Test users created:');
        $this->command->info('📧 Client: client@test.com (password: password)');
        $this->command->info('👔 Employee: employee@test.com (password: password)');
        $this->command->info('👑 Admin: admin@test.com (password: password)');
    }

    /**
     * Create main test users
     */
    private function createTestUsers()
    {
        // Create Client user
        $client = User::create([
            'name' => 'John Client',
            'email' => 'client@test.com',
            'password' => Hash::make('password'),
            'role_id' => User::ROLE_CLIENT,
            'phone' => '+1-555-0101',
            'address' => '123 Client Street, City, ST 12345',
            'date_of_birth' => '1990-01-15',
            'status' => User::STATUS_ACTIVE,
        ]);

        // Create Employee user
        $employee = User::create([
            'name' => 'Jane Employee',
            'email' => 'employee@test.com',
            'password' => Hash::make('password'),
            'role_id' => User::ROLE_EMPLOYEE,
            'phone' => '+1-555-0102',
            'address' => '456 Employee Ave, City, ST 12345',
            'date_of_birth' => '1985-06-20',
            'status' => User::STATUS_ACTIVE,
        ]);

        // Create Admin user
        $admin = User::create([
            'name' => 'Bob Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => User::ROLE_ADMIN,
            'phone' => '+1-555-0103',
            'address' => '789 Admin Blvd, City, ST 12345',
            'date_of_birth' => '1980-12-10',
            'status' => User::STATUS_ACTIVE,
        ]);

        // Create bank accounts for each user
        Account::create([
            'user_id' => $client->id,
            'account_number' => 'ACC1001',
            'account_type' => 'checking',
            'balance' => 1500.00,
            'status' => 'active'
        ]);

        Account::create([
            'user_id' => $employee->id,
            'account_number' => 'ACC1002',
            'account_type' => 'checking',
            'balance' => 2500.00,
            'status' => 'active'
        ]);

        Account::create([
            'user_id' => $admin->id,
            'account_number' => 'ACC1003',
            'account_type' => 'checking',
            'balance' => 5000.00,
            'status' => 'active'
        ]);
    }

    /**
     * Create additional sample client users
     */
    private function createSampleClients()
    {
        $sampleClients = [
            [
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
                'balance' => 750.00,
                'account_number' => 'ACC1004'
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'balance' => 2200.00,
                'account_number' => 'ACC1005'
            ],
            [
                'name' => 'Diana Wilson',
                'email' => 'diana@example.com',
                'balance' => 980.50,
                'account_number' => 'ACC1006'
            ],
            [
                'name' => 'Frank Miller',
                'email' => 'frank@example.com',
                'balance' => 3400.25,
                'account_number' => 'ACC1007'
            ],
        ];

        foreach ($sampleClients as $clientData) {
            $user = User::create([
                'name' => $clientData['name'],
                'email' => $clientData['email'],
                'password' => Hash::make('password'),
                'role_id' => User::ROLE_CLIENT,
                'phone' => '+1-555-' . rand(1000, 9999),
                'address' => rand(100, 999) . ' Sample St, City, ST 12345',
                'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years'),
                'status' => User::STATUS_ACTIVE,
            ]);

            Account::create([
                'user_id' => $user->id,
                'account_number' => $clientData['account_number'],
                'account_type' => 'checking',
                'balance' => $clientData['balance'],
                'status' => 'active'
            ]);
        }
    }

    /**
     * Create sample transactions
     */
    private function createSampleTransactions()
    {
        // Get the main client for creating transactions
        $client = User::where('email', 'client@test.com')->first();
        $clientAccount = $client?->primaryAccount();

        // Get Alice for transfer example
        $alice = User::where('email', 'alice@example.com')->first();
        $aliceAccount = $alice?->primaryAccount();

        if (! $clientAccount) {
            return;
        }

        // Sample transactions for the main client
        $transactions = [
            [
                'type' => 'deposit',
                'amount' => 500.00,
                'description' => 'Initial deposit',
                'created_at' => now()->subDays(10),
            ],
            [
                'type' => 'deposit',
                'amount' => 1000.00,
                'description' => 'Salary deposit',
                'created_at' => now()->subDays(7),
            ],
            [
                'type' => 'withdrawal',
                'amount' => 100.00,
                'description' => 'ATM withdrawal',
                'created_at' => now()->subDays(5),
            ],
            [
                'type' => 'withdrawal',
                'amount' => 50.00,
                'description' => 'Coffee shop',
                'created_at' => now()->subDays(3),
            ],
            [
                'type' => 'transfer_out',
                'amount' => 200.00,
                'description' => 'Transfer to Alice Smith',
                'created_at' => now()->subDays(2),
            ],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create([
                'account_id' => $clientAccount->id,
                'type' => $transactionData['type'],
                'amount' => $transactionData['amount'],
                'description' => $transactionData['description'],
                'created_at' => $transactionData['created_at'],
                'updated_at' => $transactionData['created_at'],
            ]);
        }

        // Create corresponding transfer_in transaction for Alice, if she exists
        if ($aliceAccount) {
            Transaction::create([
                'account_id' => $aliceAccount->id,
                'type' => 'transfer_in',
                'amount' => 200.00,
                'description' => 'Transfer from John Client',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);
        }

        // Add a few more random transactions for other users
        $this->createRandomTransactions();
    }

    /**
     * Create some random transactions for demo purposes
     */
    private function createRandomTransactions()
    {
        $users = User::where('role_id', User::ROLE_CLIENT)->with('accounts')->get();

        foreach ($users as $user) {
            $account = $user->primaryAccount();
            if (! $account) {
                continue;
            }

            // Create 2-5 random transactions per user
            $transactionCount = rand(2, 5);

            for ($i = 0; $i < $transactionCount; $i++) {
                $types = ['deposit', 'withdrawal'];
                $type = $types[array_rand($types)];
                $amount = rand(25, 500);

                $descriptions = [
                    'deposit' => ['Payroll deposit', 'Cash deposit', 'Check deposit', 'Online transfer'],
                    'withdrawal' => ['ATM withdrawal', 'Store purchase', 'Online payment', 'Gas station'],
                ];

                $description = $descriptions[$type][array_rand($descriptions[$type])];

                Transaction::create([
                    'account_id' => $account->id,
                    'type' => $type,
                    'amount' => $amount,
                    'description' => $description,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
