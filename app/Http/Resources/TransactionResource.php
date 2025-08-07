<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Transaction
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_account_id' => $this->from_account_id,
            'to_account_id' => $this->to_account_id,
            'amount' => $this->amount->value(),
            'amount_formatted' => $this->amount->toFormattedString(),
            'type' => $this->type,
            'status' => $this->status,
            'reference_id' => $this->reference_id->value(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
