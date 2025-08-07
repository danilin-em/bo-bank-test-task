<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);
        
        return $user->load('account');
    }

    public function getUserWithAccount(string $id): User
    {
        return User::with('account')->findOrFail($id);
    }
}