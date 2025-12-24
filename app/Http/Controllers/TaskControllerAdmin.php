<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Utils\Controllers\BaseController;
use App\Utils\Functions\FunctionUtils;
use Illuminate\Http\Request;

class TaskControllerAdmin extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Task::class,
            resource: TaskResource::class,
            loadRelations: ['tasked', 'tasker'],
            validation: [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'requirements' => ['nullable', 'array'],
                'requirements.*' => ['required_with:requirements', 'array'],
                'requirements.*.field' => ['required_with:requirements.*', 'string', 'max:255'],
                'requirements.*.value' => ['required_with:requirements.*', 'string'],
                'expires_at' => ['required', 'date', 'after_or_equal:today'],
            ],
            validation_update: [
                'name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'requirements' => ['nullable', 'array'],
                'requirements.*' => ['required_with:requirements', 'array'],
                'requirements.*.field' => ['required_with:requirements.*', 'string', 'max:255'],
                'requirements.*.value' => ['required_with:requirements.*', 'string'],
                'status' => ['nullable', 'string', 'in:completed'],
                'expires_at' => ['nullable', 'date', 'after_or_equal:expires_at'],
            ],
            validation_extensions: [
                'store' => [
                    'manager_id' => fn(Request $request, array $validated) => $request->user('user')->id,
                    'order' => function (Request $request, array $validated) {
                        $tasked = User::query()->findOrFail($validated['manager_id']);
                        $role = $tasked->role;
                        $order = 0;
                        while (isset($role->master_id)) {
                            $order++;
                            $role = Role::query()->findOrFail($role->master_id);
                        }
                        return $order;
                    }
                ]
            ],
            selection_query_replace: [
                "index" => fn(Request $request, array $validated) => Task::query()
                    ->whereHas('tasked', function ($query) use ($request) {
                        $query->higherRankedThan($request->user('user')->role);
                    })
            ],
            access_checks: [
                'is_higher_ranked' => function (Request $request, array $validated, string $method) {
                    $user = isset($validated['user_id']) ? User::query()->findOrFail($validated['user_id']) : null;
                    $manager = $request->user('user');
                    if ($method == "store") {
                        $user_role = $user->role;
                        $manager_role = $manager->role;
                        return FunctionUtils::isHigherRanked($user_role, $manager_role);
                    } else if (str_starts_with($method, "edit") || str_starts_with($method, "show")
                        || str_starts_with($method, "destroy") || str_starts_with($method, "restore") || str_starts_with($method, "delete")) {
                        $exploded = explode(":", $method);
                        $kw = $exploded[1];
                        $custom_kw = $exploded[2];
                        $task = Task::query()->firstWhere($custom_kw, $kw);
                        $user_role = $task->tasked->role;
                        $manager_role = $manager->role;
                        return FunctionUtils::isHigherRanked($user_role, $manager_role);
                    } else if ($method == "index") {
                        return true;
                    } else {
                        return false;
                    }
                },
            ]
        );
    }
}
