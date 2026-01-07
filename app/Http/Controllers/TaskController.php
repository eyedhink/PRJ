<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Report;
use App\Models\Task;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TaskController extends BaseController
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
                'expires_at' => ['required', 'date', 'after_or_equal:today'],
            ],
            validation_update: [
                'name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'status' => ['nullable', 'string', 'in:completed'],
                'expires_at' => ['nullable', 'date', 'after_or_equal:expires_at'],
            ],
            validation_extensions: [
                'store' => [
                    'order' => fn(Request $request, array $validated) => 0,
                    'user_id' => fn(Request $request, array $validated) => $request->user('user')->id,
                ]
            ],
            selection_query: fn(Request $request): Builder => Task::with(['tasked', 'tasker'])->where('user_id', $request->user('user')->id)->orderByDesc('order'),
            selection_query_replace: [
                "index" => fn(Request $request): Builder => Task::query()->where('user_id', $request->user('user')->id)->orderByDesc('order'),
            ],
            access_checks: [
                "is_failed" => function (Request $request, array $validated, string $method) {
                    if (str_starts_with($method, "edit") || str_starts_with($method, "show")
                        || str_starts_with($method, "destroy") || str_starts_with($method, "restore") || str_starts_with($method, "delete")) {
                        $exploded = explode(":", $method);
                        $report = Task::query()->findOrFail($exploded[1]);
                        if ($report->status == "failed") {
                            var_dump($report->status);
                            return false;
                        }
                    }
                    return true;
                }
            ]
        );
    }
}
