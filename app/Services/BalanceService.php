<?php

namespace App\Services;

use App\Models\Account;

class BalanceService
{
    public function getBalance(string $accountId): Account
    {
        return Account::findOrFail($accountId);
    }

    public function getAccountTransactions(string $accountId): \Illuminate\Database\Eloquent\Collection
    {
        $account = Account::findOrFail($accountId);
        
        return $account->transactions()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function hasEnoughFunds(Account $account, int $amount): bool
    {
        return $account->balance >= $amount;
    }
}
