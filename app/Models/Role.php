<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    // Role constants
    const REGULAR_CLIENT = 1;
    const VIP_CLIENT = 2;
    const ENTERPRISE_CLIENT = 3;
    const EMPLOYEE = 4;
    const MANAGER = 5;
    const SUPERVISOR = 6;
    const CEO = 7;
    const ADMIN = 8;

    protected $fillable = ["name", "permissions", "level"];

    protected $casts = [
        "permissions" => "array",
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Permission checks
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function canApproveTransactions()
    {
        return $this->hasPermission("approve_transactions");
    }

    public function canSearchUsers()
    {
        return $this->hasPermission("search_users");
    }

    public function canManageUsers()
    {
        return $this->hasPermission("manage_users");
    }

    public function canViewAllTransactions()
    {
        return $this->hasPermission("view_all_transactions") ||
            in_array($this->id, [self::SUPERVISOR, self::CEO, self::ADMIN]);
    }

    // Helper methods for role types
    public function isClient()
    {
        return in_array($this->id, [
            self::REGULAR_CLIENT,
            self::VIP_CLIENT,
            self::ENTERPRISE_CLIENT,
        ]);
    }

    public function isEmployee()
    {
        return in_array($this->id, [
            self::EMPLOYEE,
            self::MANAGER,
            self::SUPERVISOR,
            self::CEO,
        ]);
    }

    public function isAdmin()
    {
        return $this->id === self::ADMIN;
    }

    public function canApproveAmount($amount)
    {
        switch ($this->id) {
            case self::MANAGER:
                return $amount <= 100000;
            case self::SUPERVISOR:
                return $amount <= 500000;
            case self::CEO:
            case self::ADMIN:
                return true;
            default:
                return false;
        }
    }
}
