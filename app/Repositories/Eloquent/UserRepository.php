<?php

namespace App\Repositories\Eloquent;

use App\DTOs\UserUpdateDTO;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(Email $email): ?User
    {
        return User::where('email', $email->value())->first();
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, UserUpdateDTO $dto): bool
    {
        if (! $dto->hasChanges()) {
            return true;
        }

        return User::where('id', $id)->update($dto->toArray());
    }

    public function delete(int $id): bool
    {
        return User::destroy($id) > 0;
    }
}
