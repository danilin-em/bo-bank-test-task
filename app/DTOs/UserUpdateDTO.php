<?php

namespace App\DTOs;

use App\Http\Requests\UpdateUserRequest;
use App\ValueObjects\Email;

readonly class UserUpdateDTO
{
    public function __construct(
        public ?string $name = null,
        public ?Email  $email = null,
        public ?int    $age = null
    ) {
    }

    public static function fromRequest(UpdateUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email') ? new Email($request->validated('email')) : null,
            age: $request->validated('age')
        );
    }

    public static function create(?string $name = null, ?Email $email = null, ?int $age = null): self
    {
        return new self(
            name: $name,
            email: $email,
            age: $age
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email->value();
        }

        if ($this->age !== null) {
            $data['age'] = $this->age;
        }

        return $data;
    }

    public function hasChanges(): bool
    {
        return $this->name !== null || $this->email !== null || $this->age !== null;
    }
}
