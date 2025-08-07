<?php

namespace App\Repositories;

use App\Models\Account;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    public function findById(int $id): ?Account;

    public function findByUserId(int $userId): Collection;

    public function lockAccountsForUpdate(array $accountIds): Collection;

    public function updateBalance(int $accountId, Money $newBalance): bool;

    public function create(array $data): Account;

    public function getTotalBalance(int $accountId): Money;
}
