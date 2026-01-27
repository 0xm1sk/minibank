<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Simplified Mini Bank
|--------------------------------------------------------------------------
|
| This file contains the web routes for our mini banking application.
| We have 3 simple user types:
| - Client (role_id = 1): Regular bank customers
| - Employee (role_id = 2): Bank staff
| - Admin (role_id = 3): Bank managers
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
        case 1: // Client
            return redirect('/client/dashboard');
        case 2: // Employee
            return redirect('/employee/dashboard');
        case 3: // Admin
            return redirect('/admin/dashboard');
        default:
            auth()->logout();
            return redirect('/login')->with('error', 'Invalid user role');
    }
});

/*
|--------------------------------------------------------------------------
| Client Routes (role_id = 1)
|--------------------------------------------------------------------------
| Routes for regular bank customers who can manage their accounts
*/
Route::middleware(['auth'])->prefix('client')->group(function () {
    // Only allow clients (role_id = 1)
    Route::middleware('role:1')->group(function () {

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
| Employee Routes (role_id = 2)
|--------------------------------------------------------------------------
| Routes for bank staff who help customers
*/
Route::middleware(['auth'])->prefix('employee')->group(function () {
    // Only allow employees (role_id = 2) and admins (role_id = 3)
    Route::middleware('role:2,3')->group(function () {

        // Employee dashboard
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])
            ->name('employee.dashboard');

        // View client information
        Route::get('/clients', [EmployeeController::class, 'viewAllClients'])
            ->name('employee.clients');

        Route::get('/client/{id}', [EmployeeController::class, 'viewClientDetails'])
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
| Admin Routes (role_id = 3)
|--------------------------------------------------------------------------
| Routes for bank managers who can do everything
*/
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Only allow admins (role_id = 3)
    Route::middleware('role:3')->group(function () {

        // Admin dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        // User management
        Route::get('/users', [AdminController::class, 'allUsers'])
            ->name('admin.users');

        Route::get('/users/search', [AdminController::class, 'searchUsers'])
            ->name('admin.search');

        Route::get('/pending-requests', [AdminController::class, 'pendingRequests'])
            ->name('admin.pending-requests');

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
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
| Routes available to all authenticated users for managing their profile
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include authentication routes (login, register, etc.)
require __DIR__.'/auth.php';
