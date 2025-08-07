<?php

namespace App\Actions;

use App\DTOs\TransferDTO;
use App\Events\MoneyTransferredEvent;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidAmountException;
use App\Models\Transaction;
use App\Repositories\AccountRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

readonly class TransferMoneyAction
{
    public function __construct(
        private AccountRepositoryInterface     $accountRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    /**
     * @throws AccountNotFoundException
     * @throws InvalidAmountException
     * @throws InsufficientFundsException
     */
    public function execute(TransferDTO $dto): Transaction
    {
        if ($dto->fromAccountId === $dto->toAccountId) {
            throw new InvalidAmountException('Cannot transfer to the same account');
        }

        return DB::transaction(function () use ($dto) {
            // Lock accounts in sorted order to prevent deadlocks
            $lockedAccounts = $this->accountRepository->lockAccountsForUpdate([
                $dto->fromAccountId,
                $dto->toAccountId,
            ])->keyBy('id');

            // Check both accounts exist
            if ($lockedAccounts->count() !== 2) {
                throw new AccountNotFoundException('One or both accounts not found');
            }

            /**
             * @var \App\Models\Account $fromAccount
             * @var \App\Models\Account $toAccount
             */
            $fromAccount = $lockedAccounts[$dto->fromAccountId];
            $toAccount = $lockedAccounts[$dto->toAccountId];

            // Store original balances for event
            $originalFromBalance = $fromAccount->balance;
            $originalToBalance = $toAccount->balance;

            // Check sufficient funds
            if (! $fromAccount->balance->isGreaterThanOrEqual($dto->amount)) {
                throw new InsufficientFundsException(
                    sprintf(
                        'Insufficient funds. Available: %d cents, Required: %d cents',
                        $fromAccount->balance->value(),
                        $dto->amount->value()
                    )
                );
            }

            // Check if reference_id already exists
            if ($this->transactionRepository->findByReferenceId($dto->referenceId)) {
                throw new \InvalidArgumentException('Reference ID already exists');
            }

            // Create transaction record
            $transaction = $this->transactionRepository->createTransfer(
                $dto->fromAccountId,
                $dto->toAccountId,
                $dto->amount,
                $dto->referenceId
            );

            // Calculate new balances
            $newFromBalance = $fromAccount->balance->subtract($dto->amount);
            $newToBalance = $toAccount->balance->add($dto->amount);

            // Update balances
            $this->accountRepository->updateBalance($fromAccount->id, $newFromBalance);
            $this->accountRepository->updateBalance($toAccount->id, $newToBalance);

            // Reload accounts with fresh balances
            $updatedFromAccount = $this->accountRepository->findById($fromAccount->id);
            $updatedToAccount = $this->accountRepository->findById($toAccount->id);

            // Fire the event
            Event::dispatch(new MoneyTransferredEvent(
                $transaction,
                $updatedFromAccount,
                $updatedToAccount,
                $originalFromBalance,
                $originalToBalance,
                $newFromBalance,
                $newToBalance
            ));

            return $transaction->fresh();
        });
    }
}
