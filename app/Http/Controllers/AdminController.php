<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransferRequest;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Str;

class AdminController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Display the admin dashboard with statistics and pending requests
     */
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403, "Access denied. Admin access only.");
        }

        // Get dashboard statistics
        $stats = [
            "total_users" => User::count(),
            "total_clients" => User::whereHas("role", function ($q) {
                $q->where("name", "like", "%client%");
            })->count(),
            "total_accounts" => Account::count(),
            "total_balance" => Account::sum("balance"),
            "pending_requests" => TransferRequest::pending()->count(),
            "todays_transactions" => Transaction::whereDate(
                "created_at",
                today(),
            )->count(),
        ];

        // Get pending transfer requests
        $pendingRequests = TransferRequest::with([
            "fromAccount.user",
            "toAccount.user",
            "requestedBy",
        ])
            ->pending()
            ->orderBy("created_at", "desc")
            ->limit(10)
            ->get();

        // Get recent transactions
        $recentTransactions = Transaction::with(["account.user"])
            ->orderBy("created_at", "desc")
            ->limit(10)
            ->get();

        return view(
            "admin.dashboard",
            compact("stats", "pendingRequests", "recentTransactions"),
        );
    }

    /**
     * Get all users for main dashboard
     */
    public function allUsers()
    {
        $user = Auth::user();

        // For this simplified version, only allow Laravel-level admins (role_id = 3)
        if (! $user || ! $user->isAdmin()) {
            abort(403, "Access denied. Admin access only.");
        }

        $query = User::with(["role", "accounts"]);

        // Admin can see everyone in this simplified setup

        // Get all users with their roles and accounts
        $users = $query->orderBy("role_id")->orderBy("name")->get();

        return view("admin.all-users", compact("users"));
    }

    /**
     * Enhanced user search with filters
     */
    public function searchUsers(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, "Access denied. Admin access only.");
        }

        $search = $request->input("search", "");
        $roleFilter = $request->input("role", "");
        $statusFilter = $request->input("status", "");

        $query = User::with(["role", "accounts"]);

        // Search by name, email, or account number
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhereHas("accounts", function ($subq) use ($search) {
                        $subq->where("account_number", "like", "%{$search}%");
                    });
            });
        }

        // Filter by role
        if ($roleFilter) {
            $query->where("role_id", $roleFilter);
        }

        // Filter by status
        if ($statusFilter) {
            $query->where("status", $statusFilter);
        }

        $users = $query->orderBy("name")->paginate(20);

        // For now, just load all roles for filters
        $roles = Role::all();

        return view(
            "admin.search-results",
            compact("users", "search", "roleFilter", "statusFilter", "roles"),
        );
    }

    /**
     * Show detailed user information
     */
    public function userDetails($id)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403, "Access denied. Admin access only.");
        }

        $userDetails = User::with([
            "role",
            "accounts.transactions",
        ])->findOrFail($id);

        // Get user's transaction history
        $transactions = Transaction::whereHas("account", function ($q) use ($id) {
                $q->where("user_id", $id);
            })
            ->with(["account"])
            ->orderBy("created_at", "desc")
            ->paginate(20);

        return view(
            "admin.user-details",
            compact("userDetails", "transactions"),
        )->with("user", $userDetails);
    }

    /**
     * Show pending transfer requests for approval
     */
    public function pendingRequests()
    {
        $user = Auth::user();

        if (!$user || !$user->canApproveTransactions()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        $requests = TransferRequest::with([
            "fromAccount.user",
            "toAccount.user",
            "requestedBy",
        ])
            ->pending()
            ->orderBy("created_at", "desc")
            ->paginate(20);

        return view("admin.pending-requests", compact("requests"));
    }

    /**
     * Approve transfer request
     */
    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->canApproveTransactions()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        try {
            $transferRequest = TransferRequest::findOrFail($id);

            if (!$user->canApproveAmount($transferRequest->amount)) {
                return back()->with(
                    "error",
                    "You do not have authorization to approve this amount.",
                );
            }

            $result = $this->transferService->approveTransferRequest(
                $id,
                $user->id,
            );

            return back()->with(
                "success",
                "Transfer request approved successfully.",
            );
        } catch (\Exception $e) {
            return back()->with(
                "error",
                "Failed to approve request: " . $e->getMessage(),
            );
        }
    }

    /**
     * Reject transfer request
     */
    public function rejectRequest(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->canApproveTransactions()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        $request->validate([
            "rejection_reason" => "required|string|max:500",
        ]);

        try {
            $result = $this->transferService->rejectTransferRequest(
                $id,
                $user->id,
                $request->rejection_reason,
            );

            return back()->with("success", "Transfer request rejected.");
        } catch (\Exception $e) {
            return back()->with(
                "error",
                "Failed to reject request: " . $e->getMessage(),
            );
        }
    }

    /**
     * Show form for creating new user
     */
    public function createUserForm()
    {
        $user = Auth::user();

        if (
            !$user ||
            !in_array($user->role_id, [
                Role::MANAGER,
                Role::SUPERVISOR,
                Role::CEO,
                Role::ADMIN,
            ])
        ) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        // Check role hierarchy - users can only create roles below their level
        $allowedRoleIds = [];
        switch ($user->role_id) {
            case Role::MANAGER:
                $allowedRoleIds = [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                ];
                break;
            case Role::SUPERVISOR:
                $allowedRoleIds = [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                ];
                break;
            case Role::CEO:
                $allowedRoleIds = [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                    Role::SUPERVISOR,
                ];
                break;
            case Role::ADMIN:
                $allowedRoleIds = [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                    Role::SUPERVISOR,
                    Role::CEO,
                ];
                break;
        }

        $roles = Role::whereIn("id", $allowedRoleIds)->get();

        return view("admin.user-create", compact("roles"));
    }

    /**
     * Store newly created user
     */
    public function createUser(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->canManageUsers()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users",
            ],
            "password" => ["required", "confirmed", Rules\Password::defaults()],
            "role_id" => ["required", "integer", "exists:roles,id"],
            "phone" => ["nullable", "string", "max:20"],
            "address" => ["nullable", "string", "max:500"],
            "date_of_birth" => ["nullable", "date"],
        ]);

        $newUser = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "role_id" => $validated["role_id"],
            "phone" => $validated["phone"] ?? null,
            "address" => $validated["address"] ?? null,
            "date_of_birth" => $validated["date_of_birth"] ?? null,
            "status" => User::STATUS_ACTIVE,
        ]);

        // Create account only for client roles (1, 2, 3)
        if (
            in_array($validated["role_id"], [
                Role::REGULAR_CLIENT,
                Role::VIP_CLIENT,
                Role::ENTERPRISE_CLIENT,
            ])
        ) {
            Account::create([
                "user_id" => $newUser->id,
                "account_number" => Account::generateAccountNumber(),
                "balance" => 0,
                "account_type" =>
                    $validated["role_id"] == Role::ENTERPRISE_CLIENT
                        ? Account::TYPE_BUSINESS
                        : Account::TYPE_CHECKING,
                "status" => Account::STATUS_ACTIVE,
            ]);
        }

        return redirect()
            ->route("admin.dashboard")
            ->with("success", "User created successfully.");
    }

    /**
     * Show form for editing user
     */
    public function editUser($id)
    {
        $user = Auth::user();

        if (
            !$user ||
            !in_array($user->role_id, [
                Role::MANAGER,
                Role::SUPERVISOR,
                Role::CEO,
                Role::ADMIN,
            ])
        ) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        $userToEdit = User::with("role")->findOrFail($id);

        // Check if current user can edit this user based on role hierarchy
        if (!$this->canManageUser($user, $userToEdit)) {
            abort(
                403,
                "Access denied. Cannot edit user with higher or equal privileges.",
            );
        }

        // Get allowed roles for this user level
        $allowedRoleIds = $this->getAllowedRoleIds($user->role_id);
        $roles = Role::whereIn("id", $allowedRoleIds)->get();

        return view("admin.user-edit", compact("userToEdit", "roles"));
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->canManageUsers()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        $userToUpdate = User::findOrFail($id);

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users,email," . $id,
            ],
            "password" => ["nullable", "confirmed", Rules\Password::defaults()],
            "role_id" => ["required", "integer", "exists:roles,id"],
            "phone" => ["nullable", "string", "max:20"],
            "address" => ["nullable", "string", "max:500"],
            "date_of_birth" => ["nullable", "date"],
            "status" => ["required", "in:active,inactive,suspended"],
        ]);

        $userToUpdate->update([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "role_id" => $validated["role_id"],
            "phone" => $validated["phone"],
            "address" => $validated["address"],
            "date_of_birth" => $validated["date_of_birth"],
            "status" => $validated["status"],
        ]);

        if (!empty($validated["password"])) {
            $userToUpdate->update([
                "password" => Hash::make($validated["password"]),
            ]);
        }

        return redirect()
            ->route("admin.dashboard")
            ->with("success", "User updated successfully.");
    }

    /**
     * Delete user (soft delete recommended)
     */
    public function deleteUser($id)
    {
        $user = Auth::user();

        if (!$user || !$user->canManageUsers()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        if ($user->id == $id) {
            return redirect()
                ->route("admin.dashboard")
                ->with("error", "You cannot delete your own account.");
        }

        $userToDelete = User::findOrFail($id);

        $userToDelete->delete();

        return redirect()
            ->route("admin.dashboard")
            ->with("success", "User deleted successfully.");
    }

    /**
     * Show all transactions with filters
     */
    public function allTransactions(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->role->canViewAllTransactions()) {
            abort(403, "Access denied. Insufficient privileges.");
        }

        $query = Transaction::with(["account.user", "toAccount.user"]);

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate("created_at", ">=", $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate("created_at", "<=", $request->date_to);
        }

        // Filter by type
        if ($request->type) {
            $query->where("type", $request->type);
        }

        // Filter by amount range
        if ($request->amount_min) {
            $query->where("amount", ">=", $request->amount_min);
        }
        if ($request->amount_max) {
            $query->where("amount", "<=", $request->amount_max);
        }

        $transactions = $query->orderBy("created_at", "desc")->paginate(50);

        return view("admin.transactions", compact("transactions"));
    }

    /**
     * Generate reports
     */
    public function reports()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403, "Access denied. Admin access only.");
        }

        // Monthly transaction summary
        $monthlyStats = Transaction::selectRaw(
            '
            MONTH(created_at) as month,
            YEAR(created_at) as year,
            COUNT(*) as total_transactions,
            SUM(amount) as total_amount
        ',
        )
            ->whereYear("created_at", date("Y"))
            ->groupBy("year", "month")
            ->orderBy("year", "desc")
            ->orderBy("month", "desc")
            ->get();

        // Role distribution
        $roleStats = User::selectRaw("roles.name, COUNT(*) as count")
            ->join("roles", "users.role_id", "=", "roles.id")
            ->groupBy("roles.name")
            ->get();

        return view("admin.reports", compact("monthlyStats", "roleStats"));
    }

    /**
     * System settings
     */
    public function settings()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403, "Access denied. Admin access only.");
        }

        return view("admin.settings");
    }

    /**
     * Check if current user can manage target user based on role hierarchy
     */
    private function canManageUser(User $currentUser, User $targetUser): bool
    {
        $currentLevel = $currentUser->role_id;
        $targetLevel = $targetUser->role_id;

        switch ($currentLevel) {
            case Role::EMPLOYEE:
                return in_array($targetLevel, [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                ]);
            case Role::MANAGER:
                return in_array($targetLevel, [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                ]);
            case Role::SUPERVISOR:
                return in_array($targetLevel, [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                ]);
            case Role::CEO:
                return $targetLevel != Role::ADMIN;
            case Role::ADMIN:
                return true;
            default:
                return $currentUser->id == $targetUser->id; // Can only manage self
        }
    }

    /**
     * Get allowed role IDs for user creation/editing based on current user role
     */
    private function getAllowedRoleIds(int $currentUserRoleId): array
    {
        switch ($currentUserRoleId) {
            case Role::MANAGER:
                return [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                ];
            case Role::SUPERVISOR:
                return [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                ];
            case Role::CEO:
                return [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                    Role::SUPERVISOR,
                ];
            case Role::ADMIN:
                return [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                    Role::EMPLOYEE,
                    Role::MANAGER,
                    Role::SUPERVISOR,
                    Role::CEO,
                ];
            case Role::EMPLOYEE:
                return [
                    Role::REGULAR_CLIENT,
                    Role::VIP_CLIENT,
                    Role::ENTERPRISE_CLIENT,
                ];
            default:
                return [];
        }
    }
}
