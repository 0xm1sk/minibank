<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAction extends Model
{
    protected $fillable = ['employee_id', 'action_type', 'target_user_id'];

    public function employee() {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function targetUser() {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
