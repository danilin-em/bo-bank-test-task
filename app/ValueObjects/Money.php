<?php

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

class Money implements JsonSerializable
{
    private int $cents;

    public function __construct(int $cents)
    {
        if ($cents < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }

        $this->cents = $cents;
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function value(): int
    {
        return $this->cents;
    }

    public function toFormattedString(): string
    {
        return number_format($this->value(), 2, '.', '');
    }

    public function add(Money $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(Money $other): self
    {
        $result = $this->cents - $other->cents;
        if ($result < 0) {
            throw new InvalidArgumentException('Cannot subtract more money than available');
        }

        return new self($result);
    }

    public function multiply(int $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier cannot be negative');
        }

        return new self($this->cents * $multiplier);
    }

    public function isGreaterThan(Money $other): bool
    {
        return $this->cents > $other->cents;
    }

    public function isGreaterThanOrEqual(Money $other): bool
    {
        return $this->cents >= $other->cents;
    }

    public function isLessThan(Money $other): bool
    {
        return $this->cents < $other->cents;
    }

    public function isLessThanOrEqual(Money $other): bool
    {
        return $this->cents <= $other->cents;
    }

    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function isZero(): bool
    {
        return $this->cents === 0;
    }

    public function isPositive(): bool
    {
        return $this->cents > 0;
    }

    public function __toString(): string
    {
        return $this->toFormattedString();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'cents' => $this->cents,
            'formatted' => $this->toFormattedString(),
        ];
    }
}
