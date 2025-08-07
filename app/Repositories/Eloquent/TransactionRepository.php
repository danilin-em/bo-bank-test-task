<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\TransactionRepositoryInterface;
use App\ValueObjects\Money;
use App\ValueObjects\ReferenceId;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findByReferenceId(ReferenceId $referenceId): ?Transaction
    {
        return Transaction::where('reference_id', $referenceId->value())->first();
    }

    public function findByAccountId(int $accountId): Collection
    {
        return Transaction::where('from_account_id', $accountId)
            ->orWhere('to_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByFromAccountId(int $accountId): Collection
    {
        return Transaction::where('from_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByToAccountId(int $accountId): Collection
    {
        return Transaction::where('to_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findTransferPair(ReferenceId $referenceId): Collection
    {
        return Transaction::where('reference_id', $referenceId->value())
            ->orderBy('id')
            ->get();
    }

    public function createTransfer(
        int $fromAccountId,
        int $toAccountId,
        Money $amount,
        ReferenceId $referenceId
    ): Transaction {
        return $this->create([
            'from_account_id' => $fromAccountId,
            'to_account_id' => $toAccountId,
            'amount' => $amount->value(),
            'type' => 'transfer',
            'status' => 'completed',
            'reference_id' => $referenceId->value(),
        ]);
    }

    public function createDeposit(
        int $toAccountId,
        Money $amount,
        ReferenceId $referenceId
    ): Transaction {
        return $this->create([
            'from_account_id' => null,
            'to_account_id' => $toAccountId,
            'amount' => $amount->value(),
            'type' => 'deposit',
            'status' => 'completed',
            'reference_id' => $referenceId->value(),
        ]);
    }
}
