<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;
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
                        return $this->isHigherRanked($user_role, $manager_role);
                    } else if (str_starts_with($method, "edit") || str_starts_with($method, "show")
                        || str_starts_with($method, "destroy") || str_starts_with($method, "restore") || str_starts_with($method, "delete")) {
                        $exploded = explode(":", $method);
                        $kw = $exploded[1];
                        $custom_kw = $exploded[2];
                        $task = Task::query()->firstWhere($custom_kw ?? "id", $kw);
                        $user_role = $task->tasked->role;
                        $manager_role = $manager->role;
                        return $this->isHigherRanked($user_role, $manager_role);
                    } else if ($method == "index") {
                        return true;
                    } else {
                        return false;
                    }
                },
            ]
        );
    }

    /**
     * @template TModel of Model
     * @param class-string<TModel> $userRole
     * @param class-string<TModel> $ManagerRole
     * @return bool
     */
    // 1: Financial Manager->A
    // 2: Financial Manager->B
    // 3: Financial Manager->B->C
    // 4: Financial Manager->A->F
    // 5: Marketer->D->E
    // Only (2) is Ranked Higher than (3)
    public function isHigherRanked(string $userRole, string $ManagerRole): bool
    {
        $user_path = explode("->", $userRole->branch);
        $manager_path = explode("->", $ManagerRole->branch);
        if (count($user_path) == count($manager_path)) {
            return false;
        }
        for ($i = 0; $i < (max(count($user_path), count($manager_path))); $i++) {
            if (isset($user_path[$i]) && !isset($manager_path[$i])) {
                break;
            }
            if ($user_path[$i] != $manager_path[$i]) {
                return false;
            }
        }
        if ($userRole->depth < $ManagerRole->depth) {
            return true;
        }
        return false;
    }
}
