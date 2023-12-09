<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct(private readonly AuthService $authService)
    {
    }

    public function logout(): JsonResponse
    {
        $token = Auth::user()?->currentAccessToken();
        $token->delete();

        return response()->json(['message' => 'logout success']);
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request->email, $request->password);

        return response()->json(['token' => $this->authService->issueToken($user)]);
    }

    public function register(RegistrationRequest $request)
    {
        //Creates with user role by default
        $user = $this->authService->createUser(
            $request->name,
            $request->email,
            $request->password,
            $request->role_id ?? 3
        );

        $userResponse = new UserResource($user);

        $token = $this->authService->issueToken($user);

        return response()->json(['token' => $token, 'user' => $userResponse], 201);
    }
}
