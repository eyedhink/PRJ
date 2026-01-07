<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Utils\Controllers\BaseController;
use App\Utils\Functions\FunctionUtils;
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
                "index" => fn(Request $request, array $validated) => Report::query()->whereHas('user', function ($query) use ($request) {
                    $query->higherRankedThan($request->user('user')->role);
                }),
            ],
            access_checks: [
                'is_higher_ranked' => function (Request $request, array $validated, string $method) {
                    $manager = $request->user('user');
                    if (str_starts_with($method, "show")) {
                        $exploded = explode(":", $method);
                        $kw = $exploded[1];
                        $custom_kw = $exploded[2];
                        $report = Report::query()->firstWhere($custom_kw, $kw);
                        $user_role = $report->user->role;
                        $manager_role = $manager->role;
                        return FunctionUtils::isHigherRanked($user_role, $manager_role);
                    } else if ($method == "index") {
                        return true;
                    } else {
                        return false;
                    }
                },
                "is_failed" => function (Request $request, array $validated, string $method) {
                    if (str_starts_with($method, "edit") || str_starts_with($method, "show")
                        || str_starts_with($method, "destroy") || str_starts_with($method, "restore") || str_starts_with($method, "delete")) {
                        $exploded = explode(":", $method);
                        $report = Report::query()->findOrFail($exploded[1]);
                        if ($report->status == "failed") {
                            return false;
                        }
                    }
                    return true;
                }
            ]
        );
    }
}
