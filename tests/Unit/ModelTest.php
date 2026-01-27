<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\Transaction;

class ModelTest extends TestCase
{
    /**
     * Test User model instantiation and basic methods
     */
    public function test_user_model(): void
    {
        $user = new User();
        
        // Test basic properties
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->role_id = Role::REGULAR_CLIENT;
        $user->status = User::STATUS_ACTIVE;
        
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals(Role::REGULAR_CLIENT, $user->role_id);
        $this->assertEquals(User::STATUS_ACTIVE, $user->status);
        
        // Test role methods
        $this->assertTrue($user->isClient());
        $this->assertFalse($user->isEmployee());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->canHelpCustomers());
        $this->assertFalse($user->canManageUsers());
        $this->assertFalse($user->canApproveTransactions());
        
        // Test display methods
        $this->assertEquals('Client', $user->getRoleName());
        $this->assertStringContainsString('Active', $user->getStatusBadge());
    }
    
    /**
     * Test User model with different roles
     */
    public function test_user_roles(): void
    {
        // Test Admin
        $admin = new User();
        $admin->role_id = Role::ADMIN;
        $this->assertFalse($admin->isClient());
        $this->assertFalse($admin->isEmployee());
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->canHelpCustomers());
        $this->assertTrue($admin->canManageUsers());
        $this->assertTrue($admin->canApproveTransactions());
        $this->assertEquals('Admin', $admin->getRoleName());
        
        // Test Manager
        $manager = new User();
        $manager->role_id = Role::MANAGER;
        $this->assertFalse($manager->isClient());
        $this->assertTrue($manager->isEmployee());
        $this->assertFalse($manager->isAdmin());
        $this->assertTrue($manager->canHelpCustomers());
        $this->assertFalse($manager->canManageUsers());
        $this->assertTrue($manager->canApproveTransactions());
        $this->assertTrue($manager->canApproveAmount(50000));
        $this->assertFalse($manager->canApproveAmount(200000));
        $this->assertEquals('Manager', $manager->getRoleName());
    }
    
    /**
     * Test Account model
     */
    public function test_account_model(): void
    {
        $account = new Account();
        $account->account_type = Account::TYPE_CHECKING;
        $account->balance = 1000.50;
        $account->status = Account::STATUS_ACTIVE;
        
        $this->assertEquals(Account::TYPE_CHECKING, $account->account_type);
        $this->assertEquals(1000.50, $account->balance);
        $this->assertEquals(Account::STATUS_ACTIVE, $account->status);
        
        // Test helper methods
        $this->assertTrue($account->isActive());
        $this->assertFalse($account->isFrozen());
        $this->assertFalse($account->isInactive());
        $this->assertEquals('Checking Account', $account->getTypeLabel());
        $this->assertEquals('$1,000.50', $account->getFormattedBalance());
    }
    
    /**
     * Test Transaction model
     */
    public function test_transaction_model(): void
    {
        $transaction = new Transaction();
        $transaction->type = Transaction::TYPE_DEPOSIT;
        $transaction->amount = 500.00;
        $transaction->status = Transaction::STATUS_COMPLETED;
        
        $this->assertEquals(Transaction::TYPE_DEPOSIT, $transaction->type);
        $this->assertEquals(500.00, $transaction->amount);
        $this->assertEquals(Transaction::STATUS_COMPLETED, $transaction->status);
        
        // Test helper methods
        $this->assertTrue($transaction->isDeposit());
        $this->assertFalse($transaction->isWithdrawal());
        $this->assertFalse($transaction->isTransfer());
        $this->assertTrue($transaction->isCredit());
        $this->assertFalse($transaction->isDebit());
        $this->assertTrue($transaction->isCompleted());
        $this->assertFalse($transaction->isPending());
        
        // Test display methods
        $this->assertEquals('Deposit', $transaction->getTypeLabel());
        $this->assertEquals('$500.00', $transaction->getFormattedAmount());
        $this->assertEquals('+$500.00', $transaction->getSignedAmount());
    }
}
