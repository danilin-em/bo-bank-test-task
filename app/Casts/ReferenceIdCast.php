<?php

namespace App\Casts;

use App\ValueObjects\ReferenceId;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ReferenceIdCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ReferenceId
    {
        if ($value === null) {
            return null;
        }

        return ReferenceId::fromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ReferenceId) {
            return $value->value();
        }

        return ReferenceId::fromString($value)->value();
    }
}
