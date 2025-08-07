<?php

namespace App\Actions;

use App\DTOs\UserUpdateDTO;
use App\Events\UserUpdatedEvent;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Event;

readonly class UpdateUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws \RuntimeException
     */
    public function execute(int $userId, UserUpdateDTO $dto): User
    {
        // Find the user
        $user = $this->userRepository->findById($userId);
        if (! $user) {
            throw new UserNotFoundException("User with ID {$userId} not found");
        }

        // Store original data for event
        $originalData = [
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age,
        ];

        // Update the user
        $success = $this->userRepository->update($userId, $dto);
        if (! $success) {
            throw new \RuntimeException('Failed to update user');
        }

        // Reload the user with fresh data
        $updatedUser = $this->userRepository->findById($userId);

        // Fire the event
        Event::dispatch(new UserUpdatedEvent($updatedUser, $originalData, $dto->toArray()));

        return $updatedUser;
    }
}
