<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
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
        $recentTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

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
     * View All Transactions
     */
    public function viewTransactions()
    {
        $user = Auth::user();

        // Get all user's transactions, newest first
        $transactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20); // Show 20 per page

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
            'amount' => 'required|numeric|min:1|max:10000',
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
            DB::beginTransaction();

            // Update account balance
            $account->balance += $amount;
            $account->save();

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'account_id' => $account->id,
                'type' => 'deposit',
                'amount' => $amount,
                'description' => $description,
                'balance_after' => $account->balance
            ]);

            DB::commit();

            return redirect()->route('client.dashboard')
                ->with('success', "Successfully deposited $" . number_format($amount, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Deposit failed. Please try again.']);
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
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $account = $user->primaryAccount();

        if (!$account) {
            return back()->withErrors(['error' => 'No account found. Please contact support.']);
        }

        $amount = $request->amount;
        $description = $request->description ?? 'Withdrawal';

        // Check if user has enough money
        if ($account->balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient funds. Your balance is $' . number_format($account->balance, 2)]);
        }

        try {
            DB::beginTransaction();

            // Update account balance
            $account->balance -= $amount;
            $account->save();

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'account_id' => $account->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'description' => $description,
                'balance_after' => $account->balance
            ]);

            DB::commit();

            return redirect()->route('client.dashboard')
                ->with('success', "Successfully withdrew $" . number_format($amount, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Withdrawal failed. Please try again.']);
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
            'amount' => 'required|numeric|min:1',
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

        // Check if sender has enough money
        if ($senderAccount->balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient funds. Your balance is $' . number_format($senderAccount->balance, 2)]);
        }

        try {
            DB::beginTransaction();

            // Remove money from sender's account
            $senderAccount->balance -= $amount;
            $senderAccount->save();

            // Add money to recipient's account
            $recipientAccount->balance += $amount;
            $recipientAccount->save();

            // Create transaction record for sender (outgoing)
            Transaction::create([
                'user_id' => $sender->id,
                'account_id' => $senderAccount->id,
                'type' => 'transfer_out',
                'amount' => $amount,
                'description' => $description,
                'recipient_id' => $recipient->id,
                'balance_after' => $senderAccount->balance
            ]);

            // Create transaction record for recipient (incoming)
            Transaction::create([
                'user_id' => $recipient->id,
                'account_id' => $recipientAccount->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'description' => "Transfer from {$sender->name}",
                'sender_id' => $sender->id,
                'balance_after' => $recipientAccount->balance
            ]);

            DB::commit();

            return redirect()->route('client.dashboard')
                ->with('success', "Successfully transferred $" . number_format($amount, 2) . " to {$recipient->name}");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Transfer failed. Please try again.']);
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
