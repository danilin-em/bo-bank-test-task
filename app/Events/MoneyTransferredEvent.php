<?php

namespace App\Events;

use App\Models\Account;
use App\Models\Transaction;
use App\ValueObjects\Money;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoneyTransferredEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Transaction $transaction,
        public readonly Account $fromAccount,
        public readonly Account $toAccount,
        public readonly Money $originalFromBalance,
        public readonly Money $originalToBalance,
        public readonly Money $newFromBalance,
        public readonly Money $newToBalance
    ) {
    }

    public function getAmountTransferred(): Money
    {
        return $this->transaction->amount;
    }

    public function getFromAccountBalanceChange(): Money
    {
        return $this->newFromBalance->subtract($this->originalFromBalance);
    }

    public function getToAccountBalanceChange(): Money
    {
        return $this->newToBalance->subtract($this->originalToBalance);
    }

    public function getFromAccountId(): int
    {
        return $this->fromAccount->id;
    }

    public function getToAccountId(): int
    {
        return $this->toAccount->id;
    }

    public function getFromUserId(): int
    {
        return $this->fromAccount->user_id;
    }

    public function getToUserId(): int
    {
        return $this->toAccount->user_id;
    }

    public function getReferenceId(): string
    {
        return $this->transaction->reference_id->value();
    }
}
