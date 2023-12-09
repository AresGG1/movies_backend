<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AddMovieToFavourites;
use App\Actions\RemoveMovieFromFavourites;
use App\Http\Requests\Movies\CreateMovieRequest;
use App\Http\Requests\Movies\UpdateMovieRequest;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class MoviesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $movies = QueryBuilder::for(Movie::class)
            ->allowedFilters(['name', 'release_date', 'rating'])
            ->allowedSorts(['name', 'release_date', 'rating']);

        if (Auth::check()) {
            $userId = Auth::user()->id;

            $movies->withCount(['users as is_favourite' => function ($query) use ($userId) {
                $query->where('users.id', $userId);
            }]);
            if ($request->has('is_favourite')) {
                $isFavourite = $request->input('is_favourite') == 'true' ? 1 : 0;
                $movies->having('is_favourite', '=', $isFavourite);
            }

        }

        return response()->json($movies->get());
    }

    public function store(CreateMovieRequest $request): JsonResponse
    {
        return response()->json(Movie::create($request->toArray()), Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $userId = Auth::id();
        $movie = Movie::where('id', $id)
            ->withCount(['users as is_favourite' => function ($query) use ($userId) {
                $query->select(DB::raw('count(1)'))
                    ->where('users.id', $userId);
            }])
            ->firstOrFail();

        return response()->json($movie);
    }

    public function update(int $id, UpdateMovieRequest $request): JsonResponse
    {
        /** @var Movie $movie */
        $movie = Movie::findOrFail($id);

        $movie->update($request->toArray());

        return response()->json($movie);
    }

    public function destroy(int $id): JsonResponse
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }

    public function addToFavourites(AddMovieToFavourites $action, int $id): JsonResponse
    {
        $response = $action->handle(auth()->user(), $id)
            ? [['message' => 'Movie added to favourites'], Response::HTTP_CREATED]
            : [['message' => 'Movie already in favourites']];

        return response()->json(...$response);
    }

    public function deleteFromFavourites(RemoveMovieFromFavourites $action, int $id): JsonResponse
    {
        $response = $action->handle(auth()->user(), $id)
            ? [['message' => 'Movie removed from favourites']]
            : [['message' => 'Movie not in favourites'], 409];

        return response()->json(...$response);
    }

}
