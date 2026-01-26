<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Transaction Model - Simplified Mini Bank
 *
 * This model represents all money movements in the banking system.
 * Every deposit, withdrawal, and transfer creates a transaction record.
 *
 * Transaction Types:
 * - deposit: Money added to account
 * - withdrawal: Money removed from account
 * - transfer_out: Money sent to another user
 * - transfer_in: Money received from another user
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that can be filled when creating transactions
     */
    protected $fillable = [
        'user_id',           // Who made this transaction
        'account_id',        // Which account was affected
        'type',              // deposit, withdrawal, transfer_out, transfer_in
        'amount',            // How much money
        'description',       // What was this transaction for
        'recipient_id',      // For transfers: who received the money
        'sender_id',         // For transfers: who sent the money
        'balance_after',     // Account balance after this transaction
        'status',            // completed, pending, failed
    ];

    /**
     * Cast attributes to specific types
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // Transaction type constants - makes code easier to read
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_TRANSFER_IN = 'transfer_in';

    // Transaction status constants
    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';

    /*
    |--------------------------------------------------------------------------
    | Relationships - How transactions connect to other data
    |--------------------------------------------------------------------------
    */

    /**
     * The user who made this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The account that was affected by this transaction
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * For transfers: the user who received the money
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * For transfers: the user who sent the money
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes - Easy ways to filter transactions
    |--------------------------------------------------------------------------
    */

    /**
     * Get only transfer transactions (in or out)
     */
    public function scopeTransfers($query)
    {
        return $query->whereIn('type', [
            self::TYPE_TRANSFER_OUT,
            self::TYPE_TRANSFER_IN
        ]);
    }

    /**
     * Get only deposit transactions
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', self::TYPE_DEPOSIT);
    }

    /**
     * Get only withdrawal transactions
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', self::TYPE_WITHDRAWAL);
    }

    /**
     * Get only completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Get only pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Get transactions from the last 30 days
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods - Easy ways to check transaction details
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this is a transfer transaction
     */
    public function isTransfer()
    {
        return in_array($this->type, [
            self::TYPE_TRANSFER_OUT,
            self::TYPE_TRANSFER_IN
        ]);
    }

    /**
     * Check if this is a deposit transaction
     */
    public function isDeposit()
    {
        return $this->type === self::TYPE_DEPOSIT;
    }

    /**
     * Check if this is a withdrawal transaction
     */
    public function isWithdrawal()
    {
        return $this->type === self::TYPE_WITHDRAWAL;
    }

    /**
     * Check if this transaction adds money to the account
     */
    public function isCredit()
    {
        return in_array($this->type, [
            self::TYPE_DEPOSIT,
            self::TYPE_TRANSFER_IN
        ]);
    }

    /**
     * Check if this transaction removes money from the account
     */
    public function isDebit()
    {
        return in_array($this->type, [
            self::TYPE_WITHDRAWAL,
            self::TYPE_TRANSFER_OUT
        ]);
    }

    /**
     * Check if transaction is completed successfully
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is still pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /*
    |--------------------------------------------------------------------------
    | Display Helper Methods - Nice ways to show transaction info
    |--------------------------------------------------------------------------
    */

    /**
     * Get transaction type as readable text
     */
    public function getTypeLabel()
    {
        switch ($this->type) {
            case self::TYPE_DEPOSIT:
                return 'Deposit';
            case self::TYPE_WITHDRAWAL:
                return 'Withdrawal';
            case self::TYPE_TRANSFER_OUT:
                return 'Transfer Out';
            case self::TYPE_TRANSFER_IN:
                return 'Transfer In';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormattedAmount()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get amount with + or - prefix based on transaction type
     */
    public function getSignedAmount()
    {
        $prefix = $this->isCredit() ? '+' : '-';
        return $prefix . '$' . number_format($this->amount, 2);
    }

    /**
     * Get transaction status with nice formatting
     */
    public function getStatusBadge()
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return '<span class="badge bg-success">Completed</span>';
            case self::STATUS_PENDING:
                return '<span class="badge bg-warning">Pending</span>';
            case self::STATUS_FAILED:
                return '<span class="badge bg-danger">Failed</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    /**
     * Get CSS class for styling based on transaction type
     */
    public function getCssClass()
    {
        if ($this->isCredit()) {
            return 'text-success'; // Green for money coming in
        } elseif ($this->isDebit()) {
            return 'text-danger';  // Red for money going out
        }
        return 'text-muted';
    }

    /**
     * Get a readable description of who was involved in the transaction
     */
    public function getParticipantDescription()
    {
        if ($this->isTransfer()) {
            if ($this->type === self::TYPE_TRANSFER_OUT && $this->recipient) {
                return 'To: ' . $this->recipient->name;
            } elseif ($this->type === self::TYPE_TRANSFER_IN && $this->sender) {
                return 'From: ' . $this->sender->name;
            }
        }
        return '';
    }
}
