<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function show(string $id): UserResource
    {
        $user = $this->userService->getUserWithAccount($id);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, string $id): UserResource
    {
        $user = User::findOrFail($id);
        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return new UserResource($updatedUser);
    }
}
