<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * ClientController - Handles all client (customer) banking operations
 *
 * This controller manages:
 * - Viewing account balance
 * - Making deposits and withdrawals
 * - Transferring money to other users
 * - Viewing transaction history
 */
class ClientController extends Controller
{
    /**
     * Client Dashboard - Shows account overview
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Make sure user is a client
        if (!$user->isClient()) {
            abort(403, 'Access denied. Clients only.');
        }

        // Get user's account and recent transactions
        $account = $user->primaryAccount();
        $recentTransactions = [];
        
        if ($account) {
            $recentTransactions = $account->transactions()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('client.dashboard', [
            'user' => $user,
            'account' => $account,
            'balance' => $account ? $account->balance : 0,
            'recentTransactions' => $recentTransactions
        ]);
    }

    /**
     * View Account Balance
     */
    public function viewBalance()
    {
        $user = Auth::user();
        $account = $user->primaryAccount();

        return view('client.balance', [
            'user' => $user,
            'account' => $account,
            'balance' => $account ? $account->balance : 0
        ]);
    }

    /**
     * Show Transaction History
     */
    public function viewTransactions()
    {
        $user = Auth::user();

        // Get all user's transaction activity (including pending requests)
        $allActivity = $user->allTransactionActivity();
        
        // Convert to LengthAwarePaginator for pagination
        $currentPage = request()->get('page', 1);
        $perPage = 20;
        $currentItems = $allActivity->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $allActivity->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return view('client.transactions', [
            'user' => $user,
            'transactions' => $transactions
        ]);
    }

    /**
     * Show Deposit Form
     */
    public function showDepositForm()
    {
        return view('client.deposit');
    }

    /**
     * Process Deposit
     */
    public function makeDeposit(Request $request)
    {
        // Validate the deposit amount
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $account = $user->primaryAccount();

        if (!$account) {
            return back()->withErrors(['error' => 'No account found. Please contact support.']);
        }

        $amount = $request->amount;
        $description = $request->description ?? 'Deposit';

        try {
            $transferService = new TransferService();
            $result = $transferService->deposit($account->id, $amount, $description, $user->id);

            if (isset($result['requires_approval']) && $result['requires_approval']) {
                return redirect()->route('client.dashboard')
                    ->with('info', "Your deposit of $" . number_format($amount, 2) . " requires admin approval and is pending review.");
            }

            return redirect()->route('client.dashboard')
                ->with('success', "Deposit of $" . number_format($amount, 2) . " completed successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Deposit failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show Withdrawal Form
     */
    public function showWithdrawForm()
    {
        $user = Auth::user();
        $balance = $user->getBalance();

        return view('client.withdraw', [
            'balance' => $balance
        ]);
    }

    /**
     * Process Withdrawal
     */
    public function makeWithdrawal(Request $request)
    {
        // Validate the withdrawal amount
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $account = $user->primaryAccount();

        if (!$account) {
            return back()->withErrors(['error' => 'No account found. Please contact support.']);
        }

        $amount = $request->amount;
        $description = $request->description ?? 'Withdrawal';

        try {
            $transferService = new TransferService();
            $result = $transferService->withdraw($account->id, $amount, $description, $user->id);

            if (isset($result['requires_approval']) && $result['requires_approval']) {
                return redirect()->route('client.dashboard')
                    ->with('info', "Your withdrawal of $" . number_format($amount, 2) . " requires admin approval and is pending review.");
            }

            return redirect()->route('client.dashboard')
                ->with('success', "Withdrawal of $" . number_format($amount, 2) . " completed successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Withdrawal failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show Transfer Form
     */
    public function showTransferForm()
    {
        $user = Auth::user();
        $balance = $user->getBalance();

        return view('client.transfer', [
            'balance' => $balance
        ]);
    }

    /**
     * Process Money Transfer
     */
    public function makeTransfer(Request $request)
    {
        // Validate transfer data
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255'
        ]);

        $sender = Auth::user();
        $senderAccount = $sender->primaryAccount();

        // Find recipient
        $recipient = User::where('email', $request->recipient_email)->first();

        if (!$recipient) {
            return back()->withErrors(['recipient_email' => 'Recipient not found.']);
        }

        if ($recipient->id === $sender->id) {
            return back()->withErrors(['recipient_email' => 'You cannot transfer money to yourself.']);
        }

        $recipientAccount = $recipient->primaryAccount();

        if (!$recipientAccount) {
            return back()->withErrors(['recipient_email' => 'Recipient has no active account.']);
        }

        $amount = $request->amount;
        $description = $request->description ?? "Transfer to {$recipient->name}";

        try {
            $transferService = new TransferService();
            $result = $transferService->transfer($senderAccount->id, $recipientAccount->id, $amount, $description, $sender->id);

            if (isset($result['requires_approval']) && $result['requires_approval']) {
                return redirect()->route('client.dashboard')
                    ->with('info', "Your transfer of $" . number_format($amount, 2) . " to {$recipient->name} requires admin approval and is pending review.");
            }

            return redirect()->route('client.dashboard')
                ->with('success', "Transfer of $" . number_format($amount, 2) . " to {$recipient->name} completed successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Transfer failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Find Users for Transfer (AJAX endpoint)
     */
    public function findUser(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Search for users by name or email
        $users = User::where('role_id', User::ROLE_CLIENT)
            ->where('id', '!=', Auth::id()) // Don't include current user
            ->where('status', User::STATUS_ACTIVE) // Only active users
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
