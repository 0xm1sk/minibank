<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransferRequest;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo admin
        $admin = User::create([
            "name" => "Admin Demo",
            "email" => "admin@minibank.com",
            "password" => Hash::make("password"),
            "role_id" => Role::ADMIN,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-0001",
            "address" => "123 Bank Street, Financial District",
        ]);

        // Create demo manager
        $manager = User::create([
            "name" => "Manager Demo",
            "email" => "manager@minibank.com",
            "password" => Hash::make("password"),
            "role_id" => Role::MANAGER,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-0002",
            "address" => "124 Bank Street, Financial District",
        ]);

        // Create demo employee
        $employee = User::create([
            "name" => "Employee Demo",
            "email" => "employee@minibank.com",
            "password" => Hash::make("password"),
            "role_id" => Role::EMPLOYEE,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-0003",
            "address" => "125 Bank Street, Financial District",
        ]);

        // Create demo clients
        $regularClient = User::create([
            "name" => "John Doe",
            "email" => "john@example.com",
            "password" => Hash::make("password"),
            "role_id" => Role::REGULAR_CLIENT,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-1001",
            "address" => "456 Main Street, Anytown",
            "date_of_birth" => "1985-06-15",
        ]);

        $vipClient = User::create([
            "name" => "Jane Smith",
            "email" => "jane@example.com",
            "password" => Hash::make("password"),
            "role_id" => Role::VIP_CLIENT,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-1002",
            "address" => "789 Oak Avenue, Premium City",
            "date_of_birth" => "1980-03-22",
        ]);

        $enterpriseClient = User::create([
            "name" => "Tech Corp Inc",
            "email" => "finance@techcorp.com",
            "password" => Hash::make("password"),
            "role_id" => Role::ENTERPRISE_CLIENT,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-2001",
            "address" => "100 Business Plaza, Corporate City",
        ]);

        // Create another regular client for transfers
        $secondClient = User::create([
            "name" => "Alice Johnson",
            "email" => "alice@example.com",
            "password" => Hash::make("password"),
            "role_id" => Role::REGULAR_CLIENT,
            "status" => User::STATUS_ACTIVE,
            "phone" => "+1-555-1003",
            "address" => "321 Pine Street, Hometown",
            "date_of_birth" => "1992-11-08",
        ]);

        // Create accounts for clients
        $johnAccount = Account::create([
            "user_id" => $regularClient->id,
            "account_number" => Account::generateAccountNumber(),
            "balance" => 5000.0,
            "account_type" => Account::TYPE_CHECKING,
            "status" => Account::STATUS_ACTIVE,
        ]);

        $janeAccount = Account::create([
            "user_id" => $vipClient->id,
            "account_number" => Account::generateAccountNumber(),
            "balance" => 25000.0,
            "account_type" => Account::TYPE_SAVINGS,
            "status" => Account::STATUS_ACTIVE,
        ]);

        $techCorpAccount = Account::create([
            "user_id" => $enterpriseClient->id,
            "account_number" => Account::generateAccountNumber(),
            "balance" => 100000.0,
            "account_type" => Account::TYPE_BUSINESS,
            "status" => Account::STATUS_ACTIVE,
        ]);

        $aliceAccount = Account::create([
            "user_id" => $secondClient->id,
            "account_number" => Account::generateAccountNumber(),
            "balance" => 3500.0,
            "account_type" => Account::TYPE_CHECKING,
            "status" => Account::STATUS_ACTIVE,
        ]);

        // Create sample transactions
        Transaction::create([
            "account_id" => $johnAccount->id,
            "type" => Transaction::TYPE_DEPOSIT,
            "amount" => 1000.0,
            "description" => "Initial deposit",
            "reference_number" => "TXN" . date("Ymd") . "001",
            "status" => Transaction::STATUS_COMPLETED,
            "created_at" => now()->subDays(5),
        ]);

        Transaction::create([
            "account_id" => $johnAccount->id,
            "type" => Transaction::TYPE_WITHDRAWAL,
            "amount" => 200.0,
            "description" => "ATM withdrawal",
            "reference_number" => "TXN" . date("Ymd") . "002",
            "status" => Transaction::STATUS_COMPLETED,
            "created_at" => now()->subDays(3),
        ]);

        // Transfer from Jane to John
        $transferRef = "TXN" . date("Ymd") . "003";
        Transaction::create([
            "account_id" => $janeAccount->id,
            "to_account_id" => $johnAccount->id,
            "type" => Transaction::TYPE_TRANSFER_OUT,
            "amount" => 500.0,
            "description" => "Transfer to John Doe",
            "reference_number" => $transferRef,
            "status" => Transaction::STATUS_COMPLETED,
            "created_at" => now()->subDays(2),
        ]);

        Transaction::create([
            "account_id" => $johnAccount->id,
            "to_account_id" => $janeAccount->id,
            "type" => Transaction::TYPE_TRANSFER_IN,
            "amount" => 500.0,
            "description" => "Transfer from Jane Smith",
            "reference_number" => $transferRef,
            "status" => Transaction::STATUS_COMPLETED,
            "created_at" => now()->subDays(2),
        ]);

        // Create sample transfer request (pending approval)
        TransferRequest::create([
            "from_account_id" => $techCorpAccount->id,
            "to_account_id" => $johnAccount->id,
            "amount" => 75000.0,
            "type" => "transfer",
            "description" => "Business payment - requires approval",
            "status" => "pending",
            "requested_by" => $enterpriseClient->id,
        ]);

        // Create a deposit request (pending approval)
        TransferRequest::create([
            "to_account_id" => $janeAccount->id,
            "amount" => 60000.0,
            "type" => "deposit",
            "description" => "Large deposit - requires approval",
            "status" => "pending",
            "requested_by" => $vipClient->id,
        ]);

        // Add more sample transactions for demonstration
        $transactions = [
            [
                "account_id" => $aliceAccount->id,
                "type" => Transaction::TYPE_DEPOSIT,
                "amount" => 800.0,
                "description" => "Salary deposit",
                "created_at" => now()->subDays(1),
            ],
            [
                "account_id" => $janeAccount->id,
                "type" => Transaction::TYPE_WITHDRAWAL,
                "amount" => 300.0,
                "description" => "Cash withdrawal",
                "created_at" => now()->subHours(6),
            ],
            [
                "account_id" => $techCorpAccount->id,
                "type" => Transaction::TYPE_DEPOSIT,
                "amount" => 15000.0,
                "description" => "Business revenue",
                "created_at" => now()->subHours(2),
            ],
        ];

        foreach ($transactions as $index => $transactionData) {
            Transaction::create(
                array_merge($transactionData, [
                    "reference_number" =>
                        "TXN" .
                        date("Ymd") .
                        str_pad($index + 10, 3, "0", STR_PAD_LEFT),
                    "status" => Transaction::STATUS_COMPLETED,
                ]),
            );
        }

        $this->command->info("Sample data seeded successfully!");
        $this->command->info("Demo Users Created:");
        $this->command->info("Admin: admin@minibank.com / password");
        $this->command->info("Manager: manager@minibank.com / password");
        $this->command->info("Employee: employee@minibank.com / password");
        $this->command->info("Client (John): john@example.com / password");
        $this->command->info("VIP Client (Jane): jane@example.com / password");
        $this->command->info(
            "Enterprise (TechCorp): finance@techcorp.com / password",
        );
        $this->command->info("Client (Alice): alice@example.com / password");
    }
}
