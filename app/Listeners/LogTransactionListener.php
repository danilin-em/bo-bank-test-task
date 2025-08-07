<?php

namespace App\Listeners;

use App\Events\MoneyDepositedEvent;
use App\Events\MoneyTransferredEvent;
use App\Events\UserUpdatedEvent;
use Illuminate\Support\Facades\Log;

class LogTransactionListener
{
    public function handleUserUpdated(UserUpdatedEvent $event): void
    {
        $changes = $event->getChangedFields();

        if (empty($changes)) {
            return;
        }

        Log::info('User updated', [
            'event' => 'user_updated',
            'user_id' => $event->user->id,
            'user_email' => $event->user->email->value(),
            'changes' => $changes,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function handleMoneyDeposited(MoneyDepositedEvent $event): void
    {
        Log::info('Money deposited', [
            'event' => 'money_deposited',
            'transaction_id' => $event->transaction->id,
            'account_id' => $event->getAccountId(),
            'user_id' => $event->getUserId(),
            'amount_cents' => $event->getAmountDeposited()->value(),
            'original_balance_cents' => $event->originalBalance->value(),
            'new_balance_cents' => $event->newBalance->value(),
            'reference_id' => $event->getReferenceId(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function handleMoneyTransferred(MoneyTransferredEvent $event): void
    {
        Log::info('Money transferred', [
            'event' => 'money_transferred',
            'transaction_id' => $event->transaction->id,
            'from_account_id' => $event->getFromAccountId(),
            'to_account_id' => $event->getToAccountId(),
            'from_user_id' => $event->getFromUserId(),
            'to_user_id' => $event->getToUserId(),
            'amount_cents' => $event->getAmountTransferred()->value(),
            'from_original_balance_cents' => $event->originalFromBalance->value(),
            'from_new_balance_cents' => $event->newFromBalance->value(),
            'to_original_balance_cents' => $event->originalToBalance->value(),
            'to_new_balance_cents' => $event->newToBalance->value(),
            'reference_id' => $event->getReferenceId(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
