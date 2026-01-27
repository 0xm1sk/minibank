<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

/**
 * User Model - Simplified Mini Bank
 *
 * This represents users in our banking system.
 * We have 3 simple user types:
 * - Client (role_id = 1): Regular bank customers
 * - Employee (role_id = 2): Bank staff who help customers
 * - Admin (role_id = 3): Bank managers with full access
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // User role constants - makes code easier to read
    const ROLE_CLIENT = 1;
    const ROLE_EMPLOYEE = 2;
    const ROLE_ADMIN = 3;

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
     * Get all transactions this user has made
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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
        return $this->role_id === self::ROLE_CLIENT;
    }

    /**
     * Check if user is an employee (bank staff)
     */
    public function isEmployee()
    {
        return $this->role_id === self::ROLE_EMPLOYEE;
    }

    /**
     * Check if user is an admin (bank manager)
     */
    public function isAdmin()
    {
        return $this->role_id === self::ROLE_ADMIN;
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
            case self::ROLE_CLIENT:
                return 'Client';
            case self::ROLE_EMPLOYEE:
                return 'Employee';
            case self::ROLE_ADMIN:
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
