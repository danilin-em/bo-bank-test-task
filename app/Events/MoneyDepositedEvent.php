<?php

namespace App\Events;

use App\Models\Account;
use App\Models\Transaction;
use App\ValueObjects\Money;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoneyDepositedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Transaction $transaction,
        public readonly Account $account,
        public readonly Money $originalBalance,
        public readonly Money $newBalance
    ) {
    }

    public function getAmountDeposited(): Money
    {
        return $this->transaction->amount;
    }

    public function getBalanceChange(): Money
    {
        return $this->newBalance->subtract($this->originalBalance);
    }

    public function getAccountId(): int
    {
        return $this->account->id;
    }

    public function getUserId(): int
    {
        return $this->account->user_id;
    }

    public function getReferenceId(): string
    {
        return $this->transaction->reference_id->value();
    }
}
