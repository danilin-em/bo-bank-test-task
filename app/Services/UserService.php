<?php

namespace App\Services;

use App\DTOs\UserUpdateDTO;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;

readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function updateUser(int $id, UserUpdateDTO $dto): bool
    {
        return $this->userRepository->update($id, $dto);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserWithAccount(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            throw new UserNotFoundException("User with ID {$id} not found");
        }

        return $user->load('account');
    }

}
