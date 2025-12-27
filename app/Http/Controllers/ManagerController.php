<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Utils\Exceptions\CustomException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController
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

        $manager = Manager::query()->firstWhere('name', $validated['name']);

        if (!$manager) {
            throw new CustomException("Invalid Credentials");
        }

        if (!Hash::check($validated['password'], $manager->password)) {
            throw new CustomException("Invalid Credentials");
        }

        return response()->json([
            'token' => $manager->createToken('manager-token')->plainTextToken
        ]);
    }
}
