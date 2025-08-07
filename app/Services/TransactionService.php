<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
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
        if (!$account) {
            throw new AccountNotFoundException('Account not found');
        }

        if (!$referenceId) {
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
}
