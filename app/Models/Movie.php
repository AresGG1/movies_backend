<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $casts = [
        'is_favourite' => 'boolean',
    ];
//    protected static function booted()
//    {
//        static::retrieved(function ($movie) {
//            // Mutate the 'is_favourite' attribute to a boolean
//            $movie->is_favourite = (bool) $movie->is_favourite;
//        });
//    }

    protected $fillable = [
        'name',
        'description',
        'rating',
        'release_date'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourites');
    }
}
