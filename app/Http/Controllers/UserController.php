<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();

        return response()->json(['user' => new UserResource($user)]);
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $user = $this->userService->updateUserData($request->user_id, $request->name);
        $userResource = new UserResource($user);

        return response()->json([
            'message' => 'User data updated successfully',
            'user' => $userResource
        ]);
    }

    public function updateUserRole(UpdateRoleRequest $request): JsonResponse
    {
        $user = $this->userService->updateUserRole($request->user_id, $request->role_id);
        $userResource = new UserResource($user);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $userResource
        ]);
    }
}
