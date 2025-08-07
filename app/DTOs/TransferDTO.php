<?php

namespace App\DTOs;

use App\Http\Requests\TransferRequest;
use App\ValueObjects\Money;
use App\ValueObjects\ReferenceId;

readonly class TransferDTO
{
    public function __construct(
        public int         $fromAccountId,
        public int         $toAccountId,
        public Money       $amount,
        public ReferenceId $referenceId
    ) {
    }

    public static function fromRequest(TransferRequest $request): self
    {
        return new self(
            fromAccountId: $request->validated('from_account_id'),
            toAccountId: $request->validated('to_account_id'),
            amount: new Money($request->validated('amount')),
            referenceId: $request->validated('reference_id')
                ? ReferenceId::fromString($request->validated('reference_id'))
                : ReferenceId::generate()
        );
    }

    public static function create(
        int $fromAccountId,
        int $toAccountId,
        Money $amount,
        ?ReferenceId $referenceId = null
    ): self {
        return new self(
            fromAccountId: $fromAccountId,
            toAccountId: $toAccountId,
            amount: $amount,
            referenceId: $referenceId ?? ReferenceId::generate()
        );
    }

    public function toArray(): array
    {
        return [
            'from_account_id' => $this->fromAccountId,
            'to_account_id' => $this->toAccountId,
            'amount' => $this->amount->value(),
            'reference_id' => $this->referenceId->value(),
        ];
    }
}
