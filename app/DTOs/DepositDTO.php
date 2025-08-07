<?php

namespace App\DTOs;

use App\Http\Requests\DepositRequest;
use App\ValueObjects\Money;
use App\ValueObjects\ReferenceId;

readonly class DepositDTO
{
    public function __construct(
        public int         $accountId,
        public Money       $amount,
        public ReferenceId $referenceId
    ) {
    }

    public static function fromRequest(DepositRequest $request, int $accountId): self
    {
        return new self(
            accountId: $accountId,
            amount: new Money($request->validated('amount')),
            referenceId: $request->validated('reference_id')
                ? ReferenceId::fromString($request->validated('reference_id'))
                : ReferenceId::generate()
        );
    }

    public static function create(
        int $accountId,
        Money $amount,
        ?ReferenceId $referenceId = null
    ): self {
        return new self(
            accountId: $accountId,
            amount: $amount,
            referenceId: $referenceId ?? ReferenceId::generate()
        );
    }

    public function toArray(): array
    {
        return [
            'account_id' => $this->accountId,
            'amount' => $this->amount->value(),
            'reference_id' => $this->referenceId->value(),
        ];
    }
}
