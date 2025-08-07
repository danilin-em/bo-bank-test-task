<?php

namespace App\Repositories;

use App\DTOs\UserUpdateDTO;
use App\Models\User;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function getAll(): Collection;

    public function create(array $data): User;

    public function update(int $id, UserUpdateDTO $dto): bool;

    public function delete(int $id): bool;
}
