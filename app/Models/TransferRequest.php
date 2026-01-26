<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        "from_account_id",
        "to_account_id",
        "amount",
        "type", // 'transfer', 'deposit', 'withdrawal'
        "description",
        "status", // 'pending', 'approved', 'rejected'
        "requested_by",
        "approved_by",
        "approved_at",
        "rejection_reason",
    ];

    protected $casts = [
        "amount" => "decimal:2",
        "approved_at" => "datetime",
    ];

    // Relationships
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, "from_account_id");
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, "to_account_id");
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, "requested_by");
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, "approved_by");
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where("status", "pending");
    }

    public function scopeApproved($query)
    {
        return $query->where("status", "approved");
    }

    public function scopeRejected($query)
    {
        return $query->where("status", "rejected");
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === "pending";
    }

    public function isApproved()
    {
        return $this->status === "approved";
    }

    public function isRejected()
    {
        return $this->status === "rejected";
    }

    public function requiresApproval()
    {
        return $this->amount > 50000;
    }
}
