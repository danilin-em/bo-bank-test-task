<?php

namespace App\Http\Controllers;

use App\Actions\UpdateUserAction;
use App\DTOs\UserUpdateDTO;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UpdateUserAction $updateUserAction
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function show(string $id): UserResource
    {
        $user = $this->userService->getUserWithAccount((int) $id);

        return new UserResource($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function update(UpdateUserRequest $request, string $id): UserResource
    {
        $dto = UserUpdateDTO::fromRequest($request);
        $updatedUser = $this->updateUserAction->execute((int) $id, $dto);

        return new UserResource($updatedUser->load('account'));
    }
}
