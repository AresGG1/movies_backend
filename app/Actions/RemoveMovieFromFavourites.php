<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

class RemoveMovieFromFavourites
{
    public function handle(User $user, int $movieId): bool
    {
        if (!$user->movies()->find($movieId)) {
            return false;
        }

        $user->movies()->detach($movieId);

        return true;
    }
}
