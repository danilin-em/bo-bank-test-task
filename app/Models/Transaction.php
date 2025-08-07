<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'amount' => 'integer',
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
