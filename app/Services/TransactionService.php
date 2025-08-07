<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidAmountException;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * @throws AccountNotFoundException
     * @throws InvalidAmountException
     */
    public function deposit(string $accountId, int $amount, ?string $referenceId = null): Transaction
    {
        if ($amount <= 0) {
            throw new InvalidAmountException('Deposit amount must be greater than zero');
        }

        $account = Account::find($accountId);
        if (! $account) {
            throw new AccountNotFoundException('Account not found');
        }

        if (! $referenceId) {
            $referenceId = (string) Str::uuid();
        }

        return DB::transaction(function () use ($account, $amount, $referenceId) {
            // Check if reference_id already exists
            if (Transaction::where('reference_id', $referenceId)->exists()) {
                throw new \InvalidArgumentException('Reference ID already exists');
            }

            // Create transaction
            $transaction = Transaction::create([
                'from_account_id' => null,
                'to_account_id' => $account->id,
                'amount' => $amount,
                'type' => 'deposit',
                'status' => 'completed',
                'reference_id' => $referenceId,
            ]);

            // Update account balance
            $account->increment('balance', $amount);

            return $transaction->fresh();
        });
    }

    /**
     * @throws AccountNotFoundException
     * @throws InvalidAmountException
     * @throws InsufficientFundsException
     */
    public function transfer(string $fromAccountId, string $toAccountId, int $amount, ?string $referenceId = null): Transaction
    {
        if ($amount <= 0) {
            throw new InvalidAmountException('Transfer amount must be greater than zero');
        }

        if ($fromAccountId === $toAccountId) {
            throw new InvalidAmountException('Cannot transfer to the same account');
        }

        if (! $referenceId) {
            $referenceId = (string) Str::uuid();
        }

        return DB::transaction(function () use ($fromAccountId, $toAccountId, $amount, $referenceId) {
            // Order account IDs to prevent deadlocks when multiple transfers occur simultaneously
            $accountIds = [$fromAccountId, $toAccountId];
            sort($accountIds);

            // Lock accounts in sorted order to prevent deadlocks
            $lockedAccounts = Account::whereIn('id', $accountIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Check both accounts exist
            if ($lockedAccounts->count() !== 2) {
                throw new AccountNotFoundException('One or both accounts not found');
            }

            $fromAccount = $lockedAccounts[$fromAccountId];
            $toAccount = $lockedAccounts[$toAccountId];

            // Check sufficient funds
            if ($fromAccount->balance < $amount) {
                throw new InsufficientFundsException(
                    sprintf(
                        'Insufficient funds. Available: %d kopecks, Required: %d kopecks',
                        $fromAccount->balance,
                        $amount
                    )
                );
            }

            // Check if reference_id already exists
            if (Transaction::where('reference_id', $referenceId)->exists()) {
                throw new \InvalidArgumentException('Reference ID already exists');
            }

            // Create transaction record
            $transaction = Transaction::create([
                'from_account_id' => $fromAccountId,
                'to_account_id' => $toAccountId,
                'amount' => $amount,
                'type' => 'transfer',
                'status' => 'completed',
                'reference_id' => $referenceId,
            ]);

            // Update balances atomically
            $fromAccount->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);

            return $transaction->fresh();
        });
    }
}
