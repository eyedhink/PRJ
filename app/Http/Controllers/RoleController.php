<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Role::class,
            resource: RoleResource::class,
            loadRelations: ['master', 'slaves'],
            validation: [
                'abilities' => ['required', 'array'],
                'abilities.*' => ['required_with:abilities', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:255', 'unique:roles,title'],
                'master_id' => ['nullable', 'integer', 'exists:roles,id'],
            ],
            validation_extensions: [
                'store' => [
                    'branch' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->branch . "->" . $validated['title'] : $validated['title'],
                    'depth' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->depth + 1 : 0,
                ],
                'edit' => [
                    'branch' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->branch . "->" . $validated['title'] : $validated['title'],
                    'depth' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->depth + 1 : 0,
                ]
            ],
            selection_query_replace: [
                'index' => fn(Request $request): Builder => Role::query(),
            ]
        );
    }
}
