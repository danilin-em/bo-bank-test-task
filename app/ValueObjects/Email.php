<?php

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

class Email implements JsonSerializable
{
    private string $value;

    public function __construct(string $email)
    {
        $email = trim($email);

        if (empty($email)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        if (strlen($email) > 255) {
            throw new InvalidArgumentException('Email cannot be longer than 255 characters');
        }

        $this->value = strtolower($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
