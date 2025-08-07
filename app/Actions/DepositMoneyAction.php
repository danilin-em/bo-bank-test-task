<?php

namespace App\Actions;

use App\DTOs\DepositDTO;
use App\Events\MoneyDepositedEvent;
use App\Exceptions\AccountNotFoundException;
use App\Models\Transaction;
use App\Repositories\AccountRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

readonly class DepositMoneyAction
{
    public function __construct(
        private AccountRepositoryInterface     $accountRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    /**
     * @throws AccountNotFoundException
     */
    public function execute(DepositDTO $dto): Transaction
    {
        $account = $this->accountRepository->findById($dto->accountId);
        if (! $account) {
            throw new AccountNotFoundException('Account not found');
        }

        return DB::transaction(function () use ($account, $dto) {
            // Check if reference_id already exists
            if ($this->transactionRepository->findByReferenceId($dto->referenceId)) {
                throw new \InvalidArgumentException('Reference ID already exists');
            }

            // Store original balance for event
            $originalBalance = $account->balance;

            // Create transaction
            $transaction = $this->transactionRepository->createDeposit(
                $dto->accountId,
                $dto->amount,
                $dto->referenceId
            );

            // Update account balance
            $newBalance = $account->balance->add($dto->amount);
            $this->accountRepository->updateBalance($account->id, $newBalance);

            // Reload account with fresh balance
            $updatedAccount = $this->accountRepository->findById($account->id);

            // Fire the event
            Event::dispatch(new MoneyDepositedEvent(
                $transaction,
                $updatedAccount,
                $originalBalance,
                $newBalance
            ));

            return $transaction->fresh();
        });
    }
}
