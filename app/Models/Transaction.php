<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Casts\ReferenceIdCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 *
 * Represents a financial transaction between accounts.
 *
 * @property int $id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property \App\ValueObjects\Money $amount
 * @property string $type
 * @property string $status
 * @property \App\ValueObjects\ReferenceId $reference_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'type',
        'status',
        'reference_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class,
            'reference_id' => ReferenceIdCast::class,
        ];
    }

    /**
     * Get the account that sent this transaction.
     */
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    /**
     * Get the account that received this transaction.
     */
    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
