<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utils\Controllers\ControllerTraits\Index;
use App\Utils\Controllers\ControllerTraits\Properties;
use App\Utils\Controllers\ControllerTraits\Show;
use App\Utils\Controllers\ControllerTraits\Store;
use App\Utils\Exceptions\CustomException;
use App\Utils\Functions\FunctionUtils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

#[AllowDynamicProperties]
class UserController
{
    use Properties, Store, Index, Show;

    public function __construct()
    {
        $this->model = User::class;
        $this->resource = UserResource::class;
        $this->loadRelations = ['role', 'tasks', 'reports'];
        $this->validation = [
            "name" => ['required', 'string', 'unique:users'],
            "password" => ['required', 'string'],
            "role_id" => ['required', 'integer', 'exists:roles,id'],
        ];
        $validation_create = array_map(function ($value) {
            return $value;
        }, $this->validation);
        $this->validation_create = $validation_create;
        $this->selection_query = fn(Request $request): Builder => User::with(['role', 'tasks', 'reports'])->higherRankedThan($request->user('user')->role);
        $this->selection_query_replace = ["index" => fn(Request $request): Builder => User::with(['role'])->higherRankedThan($request->user('user')->role),];
        $this->selection_query_with_trashed = null;
    }

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

    public function indexAdmin(Request $request): JsonResponse
    {
        return FunctionUtils::automatedPaginationWithBuilder($request, User::query(), UserResource::class);
    }

    public function showAdmin(string $kw): JsonResponse
    {
        return response()->json(UserResource::make(User::with(['role', 'tasks', 'reports'])->findOrFail($kw)));
    }
}
