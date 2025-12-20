<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Exceptions\InvalidCredentialsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    /**
     * @throws InvalidCredentialsException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->firstWhere('name', $validated['name']);

        if (!$user) {
            throw new InvalidCredentialsException();
        }

        if (!Hash::check($validated['password'], $user->password)) {
            throw new InvalidCredentialsException();
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
}
