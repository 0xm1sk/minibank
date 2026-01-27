<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Simplified Mini Bank
|--------------------------------------------------------------------------
|
| This file contains the web routes for our mini banking application.
| We have multiple user types:
| - Clients (role_id = 1,2,3): Regular, VIP, Enterprise bank customers
| - Employees (role_id = 4,5,6,7): Employee, Manager, Supervisor, CEO
| - Admin (role_id = 8): Bank administrators with full access
|
*/

// Home page - redirects based on user type
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }

    $user = auth()->user();

    // Simple role-based redirects
    switch($user->role_id) {
        case 1: // Regular Client
        case 2: // VIP Client  
        case 3: // Enterprise Client
            return redirect('/client/dashboard');
        case 4: // Employee
        case 5: // Manager
        case 6: // Supervisor
        case 7: // CEO
            return redirect('/employee/dashboard');
        case 8: // Admin
            return redirect('/admin/dashboard');
        default:
            auth()->logout();
            return redirect('/login')->with('error', 'Invalid user role');
    }
});

/*
|--------------------------------------------------------------------------
| Client Routes (role_id = 1,2,3)
|--------------------------------------------------------------------------
| Routes for regular bank customers who can manage their accounts
*/
Route::middleware(['auth'])->prefix('client')->group(function () {
    // Only allow clients (role_id = 1,2,3)
    Route::middleware('role:1,2,3')->group(function () {

        // Main dashboard
        Route::get('/dashboard', [ClientController::class, 'dashboard'])
            ->name('client.dashboard');

        // Account management
        Route::get('/balance', [ClientController::class, 'viewBalance'])
            ->name('client.balance');

        // Transactions
        Route::get('/transactions', [ClientController::class, 'viewTransactions'])
            ->name('client.transactions');

        Route::get('/deposit', [ClientController::class, 'showDepositForm'])
            ->name('client.deposit.form');
        Route::post('/deposit', [ClientController::class, 'makeDeposit'])
            ->name('client.deposit.store');

        Route::get('/withdraw', [ClientController::class, 'showWithdrawForm'])
            ->name('client.withdraw.form');
        Route::post('/withdraw', [ClientController::class, 'makeWithdrawal'])
            ->name('client.withdraw.store');

        // Money transfers
        Route::get('/transfer', [ClientController::class, 'showTransferForm'])
            ->name('client.transfer.form');
        Route::post('/transfer', [ClientController::class, 'makeTransfer'])
            ->name('client.transfer.store');

        // Find other users to transfer to
        Route::get('/find-user', [ClientController::class, 'findUser'])
            ->name('client.find.user');
    });
});

/*
|--------------------------------------------------------------------------
| Employee Routes (role_id = 4,5,6,7)
|--------------------------------------------------------------------------
| Routes for bank staff who help customers
*/
Route::middleware(['auth'])->prefix('employee')->group(function () {
    // Only allow employees (role_id = 4,5,6,7) and admins (role_id = 8)
    Route::middleware('role:4,5,6,7,8')->group(function () {

        // Employee dashboard
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])
            ->name('employee.dashboard');

        // View client information
        Route::get('/clients', [EmployeeController::class, 'dashboard'])
            ->name('employee.clients');

        Route::get('/client/{id}', [EmployeeController::class, 'clientDetails'])
            ->name('employee.client.details');

        // Search for clients
        Route::get('/search', [EmployeeController::class, 'searchClients'])
            ->name('employee.search');

        // View transaction reports
        Route::get('/reports', [EmployeeController::class, 'viewReports'])
            ->name('employee.reports');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (role_id = 8) and Approval Routes (role_id = 5,6,7,8)
|--------------------------------------------------------------------------
| Routes for bank managers who can do everything
| Routes for transaction approvals (Manager, Supervisor, CEO, Admin)
*/
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Admin only routes (role_id = 8)
    Route::middleware('role:8')->group(function () {
        // Admin dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        // User management
        Route::get('/users', [AdminController::class, 'allUsers'])
            ->name('admin.users');

        Route::get('/users/search', [AdminController::class, 'searchUsers'])
            ->name('admin.search');

        Route::get('/user/create', [AdminController::class, 'createUserForm'])
            ->name('admin.user.create');
        Route::post('/user/create', [AdminController::class, 'createUser'])
            ->name('admin.user.store');

        Route::get('/user/{id}', [AdminController::class, 'userDetails'])
            ->name('admin.user.details');

        Route::get('/user/{id}/edit', [AdminController::class, 'editUser'])
            ->name('admin.user.edit');
        Route::put('/user/{id}', [AdminController::class, 'updateUser'])
            ->name('admin.user.update');

        Route::delete('/user/{id}', [AdminController::class, 'deleteUser'])
            ->name('admin.user.delete');

        // System reports and analytics
        Route::get('/reports', [AdminController::class, 'reports'])
            ->name('admin.reports');

        Route::get('/transactions', [AdminController::class, 'allTransactions'])
            ->name('admin.transactions');

        // System settings
        Route::get('/settings', [AdminController::class, 'settings'])
            ->name('admin.settings');
    });

    // Approval routes (Manager, Supervisor, CEO, Admin)
    Route::middleware('role:5,6,7,8')->group(function () {
        Route::get('/pending-requests', [AdminController::class, 'pendingRequests'])
            ->name('admin.pending-requests');

        Route::get('/requests', [AdminController::class, 'allRequests'])
            ->name('admin.requests');

        Route::post('/approve-request/{id}', [AdminController::class, 'approveRequest'])
            ->name('admin.approve-request');

        Route::post('/reject-request/{id}', [AdminController::class, 'rejectRequest'])
            ->name('admin.reject-request');
    });
});

// Include authentication routes (login, register, etc.)
require __DIR__.'/auth.php';
