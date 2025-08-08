<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
use App\Models\Account;

class BalanceService
{
    /**
     * @throws AccountNotFoundException
     */
    public function getBalance(string $accountId): Account
    {
        $account = Account::find($accountId);
        if (! $account) {
            throw new AccountNotFoundException("Account with ID {$accountId} not found");
        }

        return $account;
    }

    /**
     * @throws AccountNotFoundException
     */
    public function getAccountTransactions(string $accountId): \Illuminate\Database\Eloquent\Collection
    {
        /** @var Account $account */
        $account = Account::find($accountId);
        if (! $account) {
            throw new AccountNotFoundException("Account with ID {$accountId} not found");
        }

        return $account->transactions()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
