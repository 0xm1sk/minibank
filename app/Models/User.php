<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Transaction;

/**
 * User Model - Simplified Mini Bank
 *
 * This represents users in our banking system.
 * We have 3 simple user types:
 * - Client (role_id = 1): Regular bank customers
 * - Employee (role_id = 4): Bank staff who help customers
 * - Admin (role_id = 8): Bank managers with full access
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // User role constants - makes code easier to read
    const ROLE_CLIENT = 1; // Regular Client
    const ROLE_EMPLOYEE = 4; // Employee
    const ROLE_ADMIN = 8; // Admin

    // User status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that can be filled when creating/updating users
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'date_of_birth',
        'status',
    ];

    /**
     * The attributes that should be hidden (not shown in JSON responses)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes to specific types
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships - How users connect to other data
    |--------------------------------------------------------------------------
    */

    /**
     * Each user belongs to a role (via role_id).
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * A user can have multiple bank accounts
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get the user's primary (first) account
     */
    public function primaryAccount()
    {
        return $this->accounts()->first();
    }

    /**
     * Get all transactions this user has made (through their accounts)
     */
    public function transactions()
    {
        return Transaction::whereHas('account', function ($query) {
            $query->where('user_id', $this->id);
        });
    }

    /**
     * Get all transaction activity including pending transfer requests
     */
    public function allTransactionActivity()
    {
        // Get completed transactions from transactions table
        $transactions = $this->transactions()->get()->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
                'source' => 'transaction'
            ];
        });

        // Get pending transfer requests
        $transferRequests = TransferRequest::where('requested_by', $this->id)
            ->pending()
            ->with(['fromAccount', 'toAccount'])
            ->get()
            ->map(function ($request) {
                $accountId = null;
                $type = null;
                
                switch ($request->type) {
                    case 'deposit':
                        $accountId = $request->to_account_id;
                        $type = 'deposit';
                        break;
                    case 'withdrawal':
                        $accountId = $request->from_account_id;
                        $type = 'withdrawal';
                        break;
                    case 'transfer':
                        $accountId = $request->from_account_id;
                        $type = 'transfer_out';
                        break;
                }

                return [
                    'id' => 'req_' . $request->id,
                    'type' => $type,
                    'amount' => $request->amount,
                    'description' => $request->description,
                    'status' => 'pending',
                    'created_at' => $request->created_at,
                    'source' => 'transfer_request'
                ];
            });

        // Combine and sort by date
        return $transactions->concat($transferRequests)
            ->sortByDesc('created_at')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helper Methods - Easy ways to check what a user can do
    |--------------------------------------------------------------------------
    */

    /**
     * Check if user is a client (regular customer)
     */
    public function isClient()
    {
        return in_array($this->role_id, [Role::REGULAR_CLIENT, Role::VIP_CLIENT, Role::ENTERPRISE_CLIENT]);
    }

    /**
     * Check if user is an employee (bank staff)
     */
    public function isEmployee()
    {
        return in_array($this->role_id, [Role::EMPLOYEE, Role::MANAGER, Role::SUPERVISOR, Role::CEO]);
    }

    /**
     * Check if user is an admin (bank manager)
     */
    public function isAdmin()
    {
        return $this->role_id === Role::ADMIN;
    }

    /**
     * Check if user can help other customers (employees and admins)
     */
    public function canHelpCustomers()
    {
        return $this->isEmployee() || $this->isAdmin();
    }

    /**
     * Check if user can manage other users (only admins)
     */
    public function canManageUsers()
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can approve transactions (managers, supervisors, CEO, admin)
     */
    public function canApproveTransactions()
    {
        return in_array($this->role_id, [Role::MANAGER, Role::SUPERVISOR, Role::CEO, Role::ADMIN]);
    }

    /**
     * Check if user can approve specific amounts based on role
     */
    public function canApproveAmount($amount)
    {
        switch ($this->role_id) {
            case Role::MANAGER:
                return $amount <= 100000;
            case Role::SUPERVISOR:
                return $amount <= 500000;
            case Role::CEO:
            case Role::ADMIN:
                return true;
            default:
                return false;
        }
    }

    /**
     * Check if user can view system reports (employees and admins)
     */
    public function canViewReports()
    {
        return $this->isEmployee() || $this->isAdmin();
    }

    /*
    |--------------------------------------------------------------------------
    | Account Helper Methods - Easy ways to work with user's money
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user's total balance across all accounts
     */
    public function getTotalBalance()
    {
        return $this->accounts->sum('balance');
    }

    /**
     * Get user's primary account balance (most clients have just one account)
     */
    public function getBalance()
    {
        $account = $this->primaryAccount();
        return $account ? $account->balance : 0;
    }

    /**
     * Check if user has enough money for a transaction
     */
    public function hasEnoughBalance($amount)
    {
        return $this->getBalance() >= $amount;
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helper Methods - Check if user account is OK
    |--------------------------------------------------------------------------
    */

    /**
     * Check if user account is active and can be used
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user account is suspended (can't do banking)
     */
    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Check if user can perform banking operations
     */
    public function canDoBanking()
    {
        return $this->isActive() && !$this->isSuspended();
    }

    /*
    |--------------------------------------------------------------------------
    | Display Helper Methods - Nice ways to show user info
    |--------------------------------------------------------------------------
    */

    /**
     * Get user's role name as text (instead of number)
     */
    public function getRoleName()
    {
        switch ($this->role_id) {
            case Role::REGULAR_CLIENT:
            case Role::VIP_CLIENT:
            case Role::ENTERPRISE_CLIENT:
                return 'Client';
            case Role::EMPLOYEE:
                return 'Employee';
            case Role::MANAGER:
                return 'Manager';
            case Role::SUPERVISOR:
                return 'Supervisor';
            case Role::CEO:
                return 'CEO';
            case Role::ADMIN:
                return 'Admin';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get user's status with nice formatting
     */
    public function getStatusBadge()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return '<span class="badge bg-success">Active</span>';
            case self::STATUS_SUSPENDED:
                return '<span class="badge bg-danger">Suspended</span>';
            case self::STATUS_INACTIVE:
                return '<span class="badge bg-warning">Inactive</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    /**
     * Get formatted balance with currency symbol
     */
    public function getFormattedBalance()
    {
        return '$' . number_format($this->getBalance(), 2);
    }
}
