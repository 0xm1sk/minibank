<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display the employee dashboard with list of all clients.
     * Employees can only view client information (read-only).
     */
    public function dashboard()
    {
        // Get authenticated user (middleware ensures user is authenticated)
        $user = Auth::user();
        
        // Safety check (should be handled by middleware)
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }
        
        // Ensure user has employee role (role_id = 4,5,6,7) or admin role (role_id = 8)
        if (!in_array($user->role_id, [4, 5, 6, 7, 8])) {
            abort(403, 'Access denied. Employee or admin access only.');
        }

        // Get all clients (role_id = 1,2,3) with their accounts
        $clients = User::whereIn('role_id', [1, 2, 3])
            ->with('accounts')
            ->orderBy('name')
            ->get();

        return view('employee.dashboard', compact('clients'));
    }

    /**
     * Show details of a specific client (read-only).
     */
    public function clientDetails($id)
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Safety check
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }
        
        // Ensure user has employee role (role_id = 4,5,6,7) or admin role (role_id = 8)
        if (!in_array($user->role_id, [4, 5, 6, 7, 8])) {
            abort(403, 'Access denied. Employee or admin access only.');
        }

        // Get the client with account and transactions (all client types)
        $client = User::whereIn('role_id', [1, 2, 3])
            ->with(['accounts.transactions' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);

        return view('employee.client-details', compact('client'));
    }

    /**
     * Search for clients by name or email.
     */
    public function searchClients(Request $request)
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Safety check
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }
        
        // Ensure user has employee role (role_id = 4,5,6,7) or admin role (role_id = 8)
        if (!in_array($user->role_id, [4, 5, 6, 7, 8])) {
            abort(403, 'Access denied. Employee or admin access only.');
        }

        $search = $request->input('search', '');

        // Search clients by name or email (all client types)
        $clients = User::whereIn('role_id', [1, 2, 3])
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('accounts')
            ->orderBy('name')
            ->get();

        return view('employee.dashboard', compact('clients', 'search'));
    }
}
