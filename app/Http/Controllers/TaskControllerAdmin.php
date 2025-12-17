<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Manager;
use App\Models\Task;
use App\Utils\Controllers\BaseController;
use App\Utils\Exceptions\ImpossibleRequestException;
use Illuminate\Database\Eloquent\Builder;
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
                    'order' => function (Request $request, array $validated) {
                        $tasked = Manager::query()->findOrFail($validated['manager_id']);
                        $order = 0;
                        while (isset($tasked->manager_id)) {
                            $order++;
                            $tasked = Manager::query()->findOrFail($tasked->manager_id);
                        }
                        return $order;
                    }
                ]
            ],
            selection_query: fn(Request $request): Builder => Task::with(['tasked', 'tasker'])->orderByDesc('order'),
        );
    }
}
