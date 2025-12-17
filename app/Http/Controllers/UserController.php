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

    public function apply_role(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);
        $user = $request->user('user');
        $user->role_id = $validated['role_id'];
        $user->save();
        return response()->json(["message" => "Role Applied Successfully"]);
    }
}
