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
            loadRelations: ['master', 'slaves', 'users'],
            validation: [
                'abilities' => ['required', 'array'],
                'abilities.*' => ['required_with:abilities', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:255', 'unique:roles,title'],
                'master_id' => ['nullable', 'integer', 'exists:roles,id'],
            ],
            validation_update: [
                'abilities' => ['nullable', 'array'],
                'abilities.*' => ['required_with:abilities', 'string', 'max:255'],
                'title' => ['nullable', 'string', 'max:255', 'unique:roles,title'],
                'master_id' => ['nullable', 'integer', 'exists:roles,id'],
            ],
            validation_extensions: [
                'store' => [
                    'branch' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->branch . "->" . $validated['title'] : $validated['title'],
                    'depth' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->depth + 1 : 0,
                ],
                'edit' => [
                    'branch' => fn(Request $request, array $validated) => isset($validated['title']) ? (isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->branch . "->" . $validated['title'] : $validated['title']) : null,
                    'depth' => fn(Request $request, array $validated) => isset($validated['master_id']) ? Role::query()->find($validated['master_id'])->depth + 1 : 0,
                ]
            ],
            selection_query_replace: [
                'index' => fn(Request $request): Builder => Role::query(),
            ],
            access_checks: [
                "update_other_roles" => function (Request $request, array $validated, string $method) {
                    if (str_starts_with($method, "edit") && isset($validated['title'])) {
                        $exploded = explode(":", $method);
                        $role = Role::query()->findOrFail($exploded[1]);
                        $descendants = Role::query()
                            ->where('branch', 'like', $role->branch . '->%')
                            ->where('id', '!=', $role->id)
                            ->orderBy('branch')
                            ->get();
                        foreach ($descendants as $descendant) {
                            $newDescendantBranch = str_replace($role->branch, $validated['branch'], $descendant->branch);
                            $descendant->update(['branch' => $newDescendantBranch]);
                        }
                        return true;
                    }
                    return true;
                },
                "is_more_powerful" => function (Request $request, array $validated, string $method) {
                    if ($method == "store") {
                        if (isset($validated['master_id']) && isset($validated['abilities'])) {
                            $master_role = Role::query()->findOrFail($validated['master_id']);
                            if (in_array("*", $master_role->abilities)) {
                                return true;
                            }
                            foreach ($validated['abilities'] as $ability) {
                                if (!in_array($ability, $master_role->abilities)) {
                                    return false;
                                }
                            }
                        }
                    } else if (str_starts_with($method, "edit")) {
                        $exploded = explode(":", $method);
                        $role = Role::query()->findOrFail($exploded[1]);
                        if (isset($validated['master_id'])) {
                            $master_role = Role::query()->findOrFail($validated['master_id']);
                        } else {
                            $master_role = Role::query()->findOrFail($role->master_id);
                        }
                        if (in_array("*", $master_role->abilities)) {
                            return true;
                        }
                        foreach ($validated['abilities'] as $ability) {
                            if (!in_array($ability, $master_role->abilities)) {
                                return false;
                            }
                        }
                    }
                    return true;
                }
            ]
        );
    }
}
