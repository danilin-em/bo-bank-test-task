<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InvalidAmountException;
use App\Http\Requests\DepositRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Services\BalanceService;
use App\Services\TransactionService;

class AccountController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly TransactionService $transactionService
    ) {}

    public function getBalance(string $id): AccountResource
    {
        $account = $this->balanceService->getBalance($id);

        return new AccountResource($account);
    }

    public function getTransactions(string $id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $transactions = $this->balanceService->getAccountTransactions($id);

        return TransactionResource::collection($transactions);
    }

    /**
     * @throws AccountNotFoundException
     * @throws InvalidAmountException
     */
    public function deposit(DepositRequest $request, string $id): TransactionResource
    {
        $transaction = $this->transactionService->deposit(
            $id,
            $request->validated('amount'),
            $request->validated('reference_id')
        );

        return new TransactionResource($transaction);
    }
}
