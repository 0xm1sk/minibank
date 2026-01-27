<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransferService
{
    const APPROVAL_THRESHOLD = 50000;

    /**
     * Transfer money between accounts
     */
    public function transfer($fromAccountId, $toAccountId, $amount, $description = null, $userId = null)
    {
        DB::beginTransaction();

        try {
            $fromAccount = Account::findOrFail($fromAccountId);
            $toAccount = Account::findOrFail($toAccountId);

            // Validate accounts
            $this->validateTransfer($fromAccount, $toAccount, $amount);

            // Check if transfer requires approval
            if ($amount > self::APPROVAL_THRESHOLD) {
                return $this->createTransferRequest(
                    $fromAccount,
                    $toAccount,
                    $amount,
                    'transfer',
                    $description,
                    $userId
                );
            }

            // Execute immediate transfer
            $result = $this->executeTransfer($fromAccount, $toAccount, $amount, $description);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Transfer failed', [
                'from_account' => $fromAccountId,
                'to_account' => $toAccountId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Deposit money to account
     */
    public function deposit($accountId, $amount, $description = null, $userId = null)
    {
        DB::beginTransaction();

        try {
            $account = Account::findOrFail($accountId);

            // Check if deposit requires approval
            if ($amount > self::APPROVAL_THRESHOLD) {
                $result = $this->createTransferRequest(
                    null,
                    $account,
                    $amount,
                    'deposit',
                    $description,
                    $userId
                );
                DB::commit();
                return $result;
            }

            // Execute immediate deposit
            $result = $this->executeDeposit($account, $amount, $description);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Deposit failed', [
                'account' => $accountId,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Withdraw money from account
     */
    public function withdraw($accountId, $amount, $description = null, $userId = null)
    {
        DB::beginTransaction();

        try {
            $account = Account::findOrFail($accountId);

            // Validate withdrawal
            if (!$account->canDebit($amount)) {
                throw new Exception('Insufficient funds or account inactive');
            }

            // Check if withdrawal requires approval
            if ($amount > self::APPROVAL_THRESHOLD) {
                return $this->createTransferRequest(
                    $account,
                    null,
                    $amount,
                    'withdrawal',
                    $description,
                    $userId
                );
            }

            // Execute immediate withdrawal
            $result = $this->executeWithdrawal($account, $amount, $description);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal failed', [
                'account' => $accountId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Approve a transfer request
     */
    public function approveTransferRequest($requestId, $adminId)
    {
        DB::beginTransaction();

        try {
            $request = TransferRequest::findOrFail($requestId);

            if (!$request->isPending()) {
                throw new Exception('Transfer request is not pending');
            }

            $admin = User::findOrFail($adminId);
            if (!$admin->canApproveAmount($request->amount)) {
                throw new Exception('Insufficient authorization level');
            }

            // Execute the transfer based on type
            $result = null;
            switch ($request->type) {
                case 'transfer':
                    $result = $this->executeTransfer(
                        $request->fromAccount,
                        $request->toAccount,
                        $request->amount,
                        $request->description
                    );
                    break;
                case 'deposit':
                    $result = $this->executeDeposit(
                        $request->toAccount,
                        $request->amount,
                        $request->description
                    );
                    break;
                case 'withdrawal':
                    $result = $this->executeWithdrawal(
                        $request->fromAccount,
                        $request->amount,
                        $request->description
                    );
                    break;
            }

            // Update request status
            $request->update([
                'status' => 'approved',
                'approved_by' => $adminId,
                'approved_at' => now(),
            ]);

            DB::commit();
            return [
                'success' => true,
                'request' => $request,
                'transaction' => $result
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Transfer approval failed', [
                'request_id' => $requestId,
                'admin_id' => $adminId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reject a transfer request
     */
    public function rejectTransferRequest($requestId, $adminId, $reason = null)
    {
        DB::beginTransaction();
        
        try {
            $request = TransferRequest::findOrFail($requestId);

            if (!$request->isPending()) {
                throw new Exception('Transfer request is not pending');
            }

            // Create a transaction record for the rejected request so clients can see it
            $this->createRejectedTransaction($request, $reason);

            $request->update([
                'status' => 'rejected',
                'approved_by' => $adminId,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Transfer rejection failed', [
                'request_id' => $requestId,
                'admin_id' => $adminId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a transaction record for rejected requests so clients can see them
     */
    private function createRejectedTransaction($request, $reason)
    {
        $accountId = null;
        $type = null;
        
        // Determine account and type based on request type
        switch ($request->type) {
            case 'deposit':
                $accountId = $request->to_account_id;
                $type = Transaction::TYPE_DEPOSIT;
                break;
            case 'withdrawal':
                $accountId = $request->from_account_id;
                $type = Transaction::TYPE_WITHDRAWAL;
                break;
            case 'transfer':
                $accountId = $request->from_account_id;
                $type = Transaction::TYPE_TRANSFER_OUT;
                break;
        }

        if ($accountId && $type) {
            $account = Account::find($accountId);
            if (!$account) {
                return;
            }

            $transaction = Transaction::create([
                'account_id' => $accountId,
                'type' => $type,
                'amount' => $request->amount,
                'description' => ($request->description ?? 'Transaction') . ' (REJECTED: ' . $reason . ')',
                'reference_number' => $this->generateReferenceNumber(),
                'status' => Transaction::STATUS_FAILED, // Use 'failed' to match database schema
            ]);
        }
    }

    /**
     * Create a transfer request for approval
     */
    private function createTransferRequest($fromAccount, $toAccount, $amount, $type, $description, $userId)
    {
        $request = TransferRequest::create([
            'from_account_id' => $fromAccount ? $fromAccount->id : null,
            'to_account_id' => $toAccount ? $toAccount->id : null,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'requested_by' => $userId,
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'requires_approval' => true,
            'request' => $request,
            'message' => "Transaction requires approval due to amount exceeding $" . number_format(self::APPROVAL_THRESHOLD)
        ];
    }

    /**
     * Execute transfer between accounts
     */
    private function executeTransfer($fromAccount, $toAccount, $amount, $description)
    {
        // Debit from source account
        $fromAccount->debit($amount);

        // Credit to destination account
        $toAccount->credit($amount);

        $referenceNumber = $this->generateReferenceNumber();

        // Create transaction records
        $outTransaction = Transaction::create([
            'account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'type' => Transaction::TYPE_TRANSFER_OUT,
            'amount' => $amount,
            'description' => $description ?: "Transfer to account {$toAccount->account_number}",
            'reference_number' => $referenceNumber,
            'status' => Transaction::STATUS_COMPLETED,
        ]);

        $inTransaction = Transaction::create([
            'account_id' => $toAccount->id,
            'to_account_id' => $fromAccount->id,
            'type' => Transaction::TYPE_TRANSFER_IN,
            'amount' => $amount,
            'description' => $description ?: "Transfer from account {$fromAccount->account_number}",
            'reference_number' => $referenceNumber,
            'status' => Transaction::STATUS_COMPLETED,
        ]);

        return [
            'success' => true,
            'requires_approval' => false,
            'transactions' => [$outTransaction, $inTransaction],
            'reference_number' => $referenceNumber
        ];
    }

    /**
     * Execute deposit to account
     */
    private function executeDeposit($account, $amount, $description)
    {
        $account->credit($amount);

        $transaction = Transaction::create([
            'account_id' => $account->id,
            'type' => Transaction::TYPE_DEPOSIT,
            'amount' => $amount,
            'description' => $description ?: 'Account deposit',
            'reference_number' => $this->generateReferenceNumber(),
            'status' => Transaction::STATUS_COMPLETED,
        ]);

        return [
            'success' => true,
            'requires_approval' => false,
            'transaction' => $transaction
        ];
    }

    /**
     * Execute withdrawal from account
     */
    private function executeWithdrawal($account, $amount, $description)
    {
        $account->debit($amount);

        $transaction = Transaction::create([
            'account_id' => $account->id,
            'type' => Transaction::TYPE_WITHDRAWAL,
            'amount' => $amount,
            'description' => $description ?: 'Account withdrawal',
            'reference_number' => $this->generateReferenceNumber(),
            'status' => Transaction::STATUS_COMPLETED,
        ]);

        return [
            'success' => true,
            'requires_approval' => false,
            'transaction' => $transaction
        ];
    }

    /**
     * Validate transfer conditions
     */
    private function validateTransfer($fromAccount, $toAccount, $amount)
    {
        if (!$fromAccount->isActive()) {
            throw new Exception('Source account is not active');
        }

        if (!$toAccount->isActive()) {
            throw new Exception('Destination account is not active');
        }

        if ($amount <= 0) {
            throw new Exception('Transfer amount must be positive');
        }

        if (!$fromAccount->canDebit($amount)) {
            throw new Exception('Insufficient funds in source account');
        }

        if ($fromAccount->id === $toAccount->id) {
            throw new Exception('Cannot transfer to the same account');
        }
    }

    /**
     * Generate unique reference number
     */
    private function generateReferenceNumber()
    {
        do {
            $reference = 'TXN' . date('Ymd') . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Transaction::where('reference_number', $reference)->exists());

        return $reference;
    }
}
