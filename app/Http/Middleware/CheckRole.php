<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Check if user is authenticated and has the required role.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // If user is not authenticated, redirect to login
        // Note: The 'auth' middleware should handle this first, but we check anyway for safety
        if (!Auth::check()) {
            return redirect()
                ->route("login")
                ->with("error", "Please log in to access this page.");
        }

        $user = Auth::user();

        // Parse roles from comma-separated strings or individual parameters
        $allowedRoles = [];
        foreach ($roles as $role) {
            if (is_string($role) && strpos($role, ",") !== false) {
                $allowedRoles = array_merge($allowedRoles, explode(",", $role));
            } else {
                $allowedRoles[] = $role;
            }
        }

        // Convert to integers and check if user has one of the required roles
        $allowedRoles = array_map("intval", $allowedRoles);
        if (in_array($user->role_id, $allowedRoles)) {
            return $next($request);
        }

        // User doesn't have the required role - redirect based on their actual role
        if (in_array($user->role_id, [1, 2, 3])) {
            return redirect()
                ->route("client.dashboard")
                ->with("error", "Unauthorized access!");
        } elseif (in_array($user->role_id, [4, 5, 6, 7])) {
            return redirect()
                ->route("employee.dashboard")
                ->with("error", "Unauthorized access!");
        } elseif ($user->role_id == 8) {
            return redirect()
                ->route("admin.dashboard")
                ->with("error", "Unauthorized access!");
        }

        // Fallback: redirect to login if role is unknown
        return redirect()->route("login")->with("error", "Invalid user role.");
    }
}
