<?php

declare(strict_types=1);

namespace App\Service;

use App\Http\Requests\Auth\RoleUpdateRequest;
use App\Models\Role;
use App\Models\User;

class UserService
{
    public function updateUserData(int $userId, string $username): User
    {
        $user = User::query()->findOrfail($userId);
        $user->update([
            'name' => $username
        ]);

        return $user;
    }

    public function updateUserRole(int $userId, int $roleId): User
    {
        $user = User::query()->findOrFail($userId);
        $user->role_id = $roleId;
        $user->save();

        return $user;
    }
}
