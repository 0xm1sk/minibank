<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Account Model - Simplified Mini Bank
 *
 * This model represents bank accounts in our system.
 * Each user can have one or more accounts, but most clients have just one.
 *
 * Account Types:
 * - checking: Regular checking account (most common)
 * - savings: Savings account with higher interest
 * - business: Business account for companies
 */
class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that can be filled when creating accounts
     */
    protected $fillable = [
        'user_id',          // Who owns this account
        'account_number',   // Unique account number
        'account_type',     // checking, savings, business
        'balance',          // How much money is in the account
        'status',           // active, inactive, frozen
    ];

    /**
     * Cast attributes to specific types
     */
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // Account type constants
    const TYPE_CHECKING = 'checking';
    const TYPE_SAVINGS = 'savings';
    const TYPE_BUSINESS = 'business';

    // Account status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_FROZEN = 'frozen';

    /**
     * Automatically generate account number when creating new accounts
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (!$account->account_number) {
                $account->account_number = self::generateAccountNumber();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships - How accounts connect to other data
    |--------------------------------------------------------------------------
    */

    /**
     * The user who owns this account
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All transactions for this account
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Recent transactions (last 10)
     */
    public function recentTransactions()
    {
        return $this->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(10);
    }

    /*
    |--------------------------------------------------------------------------
    | Account Operations - Adding and removing money
    |--------------------------------------------------------------------------
    */

    /**
     * Check if account can have money removed from it
     */
    public function canDebit($amount)
    {
        return $this->balance >= $amount && $this->isActive();
    }

    /**
     * Remove money from account (withdrawal, transfer out)
     */
    public function debit($amount)
    {
        if (!$this->canDebit($amount)) {
            throw new \Exception('Insufficient funds or account is not active');
        }

        $this->balance -= $amount;
        return $this->save();
    }

    /**
     * Add money to account (deposit, transfer in)
     */
    public function credit($amount)
    {
        if (!$this->isActive()) {
            throw new \Exception('Cannot add money to inactive account');
        }

        $this->balance += $amount;
        return $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods - Easy ways to check account status
    |--------------------------------------------------------------------------
    */

    /**
     * Check if account is active and can be used
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if account is frozen (can't do transactions)
     */
    public function isFrozen()
    {
        return $this->status === self::STATUS_FROZEN;
    }

    /**
     * Check if account is inactive
     */
    public function isInactive()
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /*
    |--------------------------------------------------------------------------
    | Display Helper Methods - Nice ways to show account info
    |--------------------------------------------------------------------------
    */

    /**
     * Get balance formatted with currency symbol
     */
    public function getFormattedBalance()
    {
        return '$' . number_format($this->balance, 2);
    }

    /**
     * Get account type as readable text
     */
    public function getTypeLabel()
    {
        switch ($this->account_type) {
            case self::TYPE_CHECKING:
                return 'Checking Account';
            case self::TYPE_SAVINGS:
                return 'Savings Account';
            case self::TYPE_BUSINESS:
                return 'Business Account';
            default:
                return 'Unknown Account Type';
        }
    }

    /**
     * Get account status with nice formatting
     */
    public function getStatusBadge()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return '<span class="badge bg-success">Active</span>';
            case self::STATUS_FROZEN:
                return '<span class="badge bg-warning">Frozen</span>';
            case self::STATUS_INACTIVE:
                return '<span class="badge bg-danger">Inactive</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    /**
     * Get masked account number for display (shows last 4 digits)
     */
    public function getMaskedAccountNumber()
    {
        if (strlen($this->account_number) <= 4) {
            return $this->account_number;
        }

        $masked = str_repeat('*', strlen($this->account_number) - 4);
        $lastFour = substr($this->account_number, -4);

        return $masked . $lastFour;
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate a unique account number
     */
    public static function generateAccountNumber()
    {
        do {
            // Format: ACC + 7 random digits
            $number = 'ACC' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (self::where('account_number', $number)->exists());

        return $number;
    }

    /**
     * Find account by account number
     */
    public static function findByAccountNumber($accountNumber)
    {
        return self::where('account_number', $accountNumber)->first();
    }
}
