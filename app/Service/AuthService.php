<?php

declare(strict_types=1);

namespace App\Service;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;

class AuthService
{
    public function __construct(
        private readonly Hasher $hash
    ) {
    }

    public function createUser(string $name, string $email, string $password, int $roleId=3): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $this->hash->make($password),
            'role_id' => $roleId
        ]);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(string $email, string $password): User
    {
        $user = User::where(['email' => $email])->with('role')->firstOrFail();

        if (!$this->hash->check($password, $user->password)) {
            throw new InvalidCredentialsException("wrong password");
        }

        return $user;
    }

    public function issueToken(User $user): string
    {
        return $user->createToken('auth-token', $this->getAbilities($user->role->name))->plainTextToken;
    }

    public function getAbilities(string $role): array
    {
        match ($role) {
            'admin' => $abilities = ['manage.users', 'manage.movies'],
            'user' => $abilities = ['manage.movies'],
            default => $abilities = []
        };

        return $abilities;
    }
}
