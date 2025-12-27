<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utils\Exceptions\CustomException;
use App\Utils\Functions\FunctionUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    /**
     * @throws CustomException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->firstWhere('name', $validated['name']);

        if (!$user) {
            throw new CustomException("Invalid Credentials");
        }

        if (!Hash::check($validated['password'], $user->password)) {
            throw new CustomException("Invalid Credentials");
        }

        return response()->json([
            'token' => $user->createToken('user-token')->plainTextToken
        ]);
    }

    public function apply_role(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);
        $user = User::query()->findOrFail($id);
        $user->role_id = $validated['role_id'];
        $user->save();
        return response()->json(["message" => "Role Applied Successfully"]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => ['required', 'string', 'unique:users'],
            "password" => ['required', 'string'],
            "role_id" => ['required', 'integer', 'exists:roles,id'],
        ]);
        User::query()->create($validated);
        return response()->json(["message" => "User Created Successfully"]);
    }

    public function index(Request $request): JsonResponse
    {
        return FunctionUtils::automatedPaginationWithBuilder($request, User::query()->higherRankedThan($request->user('user')->role), UserResource::class);
    }

    public function show(Request $request, string $kw): JsonResponse
    {
        return response()->json(UserResource::make(User::with(['role', 'tasks', 'reports'])->higherRankedThan($request->user('user')->role)->findOrFail($kw)));
    }

    public function indexAdmin(Request $request): JsonResponse
    {
        return FunctionUtils::automatedPaginationWithBuilder($request, User::query(), UserResource::class);
    }

    public function showAdmin(Request $request, string $kw): JsonResponse
    {
        return response()->json(UserResource::make(User::with(['role', 'tasks', 'reports'])->findOrFail($kw)));
    }
}
