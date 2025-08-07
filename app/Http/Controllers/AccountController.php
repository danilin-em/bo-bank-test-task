<?php

namespace App\Http\Controllers;

use App\Actions\DepositMoneyAction;
use App\DTOs\DepositDTO;
use App\Exceptions\AccountNotFoundException;
use App\Http\Requests\DepositRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Services\BalanceService;

class AccountController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly DepositMoneyAction $depositMoneyAction
    ) {
    }

    /**
     * @throws AccountNotFoundException
     */
    public function getBalance(string $id): AccountResource
    {
        $account = $this->balanceService->getBalance($id);

        return new AccountResource($account);
    }

    /**
     * @throws AccountNotFoundException
     */
    public function getTransactions(string $id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $transactions = $this->balanceService->getAccountTransactions($id);

        return TransactionResource::collection($transactions);
    }

    /**
     * @throws AccountNotFoundException
     */
    public function deposit(DepositRequest $request, string $id): TransactionResource
    {
        $dto = DepositDTO::fromRequest($request, (int) $id);
        $transaction = $this->depositMoneyAction->execute($dto);

        return new TransactionResource($transaction);
    }
}
