<?php

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

class ReferenceId implements JsonSerializable
{
    private string $value;

    public function __construct(?string $referenceId = null)
    {
        if ($referenceId === null) {
            $this->value = (string) Uuid::uuid4();
        } else {
            $referenceId = trim($referenceId);

            if (empty($referenceId)) {
                throw new InvalidArgumentException('Reference ID cannot be empty');
            }

            if (strlen($referenceId) > 255) {
                throw new InvalidArgumentException('Reference ID cannot be longer than 255 characters');
            }

            $this->value = $referenceId;
        }
    }

    public static function generate(): self
    {
        return new self();
    }

    public static function fromString(string $referenceId): self
    {
        return new self($referenceId);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ReferenceId $other): bool
    {
        return $this->value === $other->value;
    }

    public function isUuid(): bool
    {
        return Uuid::isValid($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
