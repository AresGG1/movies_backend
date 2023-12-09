<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Movie;
use App\Models\User;

class AddMovieToFavourites
{
    public function handle(User $user, int $movieId): bool
    {
       if ($user->movies()->find($movieId)) {
           return false;
       }

       //In order to prevent setting not existing movie as favourite
       Movie::findOrFail($movieId);

       $user->movies()->attach($movieId);

       return true;
    }
}
