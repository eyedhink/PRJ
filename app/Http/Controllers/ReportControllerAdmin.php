<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReportControllerAdmin extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Report::class,
            resource: ReportResource::class,
            loadRelations: ['user'],
            validation: [
                'content' => ['required', 'string'],
            ],
            selection_query_replace: [
                "index" => fn(Request $request, array $validated) => Report::query()->cursor()->filter(function (Report $report) use ($request) {
                    return $this->isHigherRanked($report->user->role, $request->user('user')->role);
                })->values(),
            ],
            access_checks: [
                'is_higher_ranked' => function (Request $request, array $validated, string $method) {
                    $manager = $request->user('user');
                    if (starts_with($method, "show")) {
                        $exploded = explode(":", $method);
                        $kw = $exploded[1];
                        $custom_kw = $exploded[2];
                        $report = Report::query()->firstWhere($custom_kw ?? "id", $kw);
                        $user_role = $report->user->role;
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
    public function isHigherRanked(string $userRole, string $ManagerRole): bool
    {
        while (isset($userRole->master_id)) {
            $role = Role::query()->findOrFail($userRole->master_id);
            if ($role->title == $ManagerRole->title) {
                return true;
            }
        }
        return false;
    }
}
