<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidAmountException;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    /**
     * @throws AccountNotFoundException
     * @throws InvalidAmountException
     * @throws InsufficientFundsException
     */
    public function transfer(TransferRequest $request): TransactionResource
    {
        $transaction = $this->transactionService->transfer(
            $request->validated('from_account_id'),
            $request->validated('to_account_id'),
            $request->validated('amount'),
            $request->validated('reference_id')
        );

        return new TransactionResource($transaction);
    }
}
