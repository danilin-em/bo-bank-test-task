<?php

namespace App\Http\Controllers;

use App\Actions\TransferMoneyAction;
use App\DTOs\TransferDTO;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidAmountException;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferMoneyAction $transferMoneyAction
    ) {
    }

    /**
     * @throws AccountNotFoundException
     * @throws InvalidAmountException
     * @throws InsufficientFundsException
     */
    public function transfer(TransferRequest $request): TransactionResource
    {
        $dto = TransferDTO::fromRequest($request);
        $transaction = $this->transferMoneyAction->execute($dto);

        return new TransactionResource($transaction);
    }
}
