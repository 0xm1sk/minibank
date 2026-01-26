<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role Middleware - Simple access control for Mini Bank
 *
 * This middleware checks if a user has the right role to access a page.
 *
 * Usage in routes:
 * Route::middleware('role:1')->group(function() { ... });     // Only clients
 * Route::middleware('role:2,3')->group(function() { ... });   // Employees and admins
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request and check user role
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$allowedRoles - List of role IDs that can access this route
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        // First check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }

        $user = Auth::user();
        $userRoleId = $user->role_id;

        // Convert allowed roles to integers (in case they come as strings)
        $allowedRoles = array_map(function($role) {
            return (int) $role;
        }, $allowedRoles);

        // Check if user's role is in the allowed roles list
        if (in_array($userRoleId, $allowedRoles)) {
            // User has permission, continue to the requested page
            return $next($request);
        }

        // User doesn't have permission
        // Redirect them to their appropriate dashboard with an error message
        $redirectRoute = $this->getRedirectRoute($userRoleId);

        return redirect()->route($redirectRoute)
            ->with('error', 'You do not have permission to access that page.');
    }

    /**
     * Get the appropriate dashboard route for a user based on their role
     *
     * @param int $roleId
     * @return string
     */
    private function getRedirectRoute(int $roleId): string
    {
        switch ($roleId) {
            case 1: // Client
                return 'client.dashboard';
            case 2: // Employee
                return 'employee.dashboard';
            case 3: // Admin
                return 'admin.dashboard';
            default:
                return 'login'; // Unknown role, send to login
        }
    }
}
