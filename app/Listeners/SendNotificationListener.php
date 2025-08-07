<?php

namespace App\Listeners;

use App\Events\MoneyDepositedEvent;
use App\Events\MoneyTransferredEvent;
use App\Events\UserUpdatedEvent;
use Illuminate\Support\Facades\Log;

class SendNotificationListener
{
    public function handleUserUpdated(UserUpdatedEvent $event): void
    {
        $changes = $event->getChangedFields();

        if (empty($changes)) {
            return;
        }

        Log::debug('Notification stub: User profile updated', [
            'type' => 'user_profile_updated',
            'user_id' => $event->user->id,
            'user_email' => $event->user->email->value(),
            'changes_count' => count($changes),
            'message' => 'Your profile has been successfully updated.',
        ]);

        // TODO: Implement actual notification sending
    }

    public function handleMoneyDeposited(MoneyDepositedEvent $event): void
    {
        Log::debug('Notification stub: Money deposited', [
            'type' => 'money_deposited',
            'user_id' => $event->getUserId(),
            'account_id' => $event->getAccountId(),
            'amount_cents' => $event->getAmountDeposited()->value(),
            'new_balance_cents' => $event->newBalance->value(),
            'message' => sprintf(
                'Your account has been credited with %.2f cents. New balance: %.2f cents.',
                $event->getAmountDeposited()->value(),
                $event->newBalance->value()
            ),
        ]);

        // TODO: Implement actual notification sending
    }

    public function handleMoneyTransferred(MoneyTransferredEvent $event): void
    {
        Log::debug('Notification stub: Money sent', [
            'type' => 'money_sent',
            'user_id' => $event->getFromUserId(),
            'account_id' => $event->getFromAccountId(),
            'amount_cents' => $event->getAmountTransferred()->value(),
            'new_balance_cents' => $event->newFromBalance->value(),
            'to_account_id' => $event->getToAccountId(),
            'message' => sprintf(
                'You sent %.2f cents to account #%d. New balance: %.2f cents.',
                $event->getAmountTransferred()->value(),
                $event->getToAccountId(),
                $event->newFromBalance->value()
            ),
        ]);

        Log::debug('Notification stub: Money received', [
            'type' => 'money_received',
            'user_id' => $event->getToUserId(),
            'account_id' => $event->getToAccountId(),
            'amount_cents' => $event->getAmountTransferred()->value(),
            'new_balance_cents' => $event->newToBalance->value(),
            'from_account_id' => $event->getFromAccountId(),
            'message' => sprintf(
                'You received %.2f cents from account #%d. New balance: %.2f cents.',
                $event->getAmountTransferred()->value(),
                $event->getFromAccountId(),
                $event->newToBalance->value()
            ),
        ]);

        // TODO: Implement actual notification sending
    }
}
