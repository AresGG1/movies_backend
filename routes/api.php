<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum', 'ability:manage.users'])->patch('/users/role', [UserController::class, 'updateUserRole']);
Route::middleware('auth:sanctum')->patch('/users', [UserController::class, 'updateUser']);

Route::resource('movies', MoviesController::class);
Route::middleware('auth:sanctum')->post('/movies/{id}/favourites', [MoviesController::class, 'addToFavourites']);
Route::middleware('auth:sanctum')->delete('/movies/{id}/favourites', [MoviesController::class, 'deleteFromFavourites']);



