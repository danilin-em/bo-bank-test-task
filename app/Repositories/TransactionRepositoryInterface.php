<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\ValueObjects\Money;
use App\ValueObjects\ReferenceId;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function findByReferenceId(ReferenceId $referenceId): ?Transaction;

    public function findByAccountId(int $accountId): Collection;

    public function findByFromAccountId(int $accountId): Collection;

    public function findByToAccountId(int $accountId): Collection;

    public function findTransferPair(ReferenceId $referenceId): Collection;

    public function createTransfer(
        int $fromAccountId,
        int $toAccountId,
        Money $amount,
        ReferenceId $referenceId
    ): Transaction;

    public function createDeposit(
        int $toAccountId,
        Money $amount,
        ReferenceId $referenceId
    ): Transaction;
}
