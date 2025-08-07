<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Repositories\AccountRepositoryInterface;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository implements AccountRepositoryInterface
{
    public function findById(int $id): ?Account
    {
        return Account::find($id);
    }

    public function findByUserId(int $userId): Collection
    {
        return Account::where('user_id', $userId)->get();
    }

    public function lockAccountsForUpdate(array $accountIds): Collection
    {
        sort($accountIds);

        return Account::whereIn('id', $accountIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    public function updateBalance(int $accountId, Money $newBalance): bool
    {
        return Account::where('id', $accountId)
            ->update(['balance' => $newBalance->value()]);
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function getTotalBalance(int $accountId): Money
    {
        $account = $this->findById($accountId);

        if (! $account) {
            throw new \InvalidArgumentException("Account with ID {$accountId} not found");
        }

        return new Money($account->balance);
    }
}
