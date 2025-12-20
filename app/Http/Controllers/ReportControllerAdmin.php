<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

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
        $user_path = explode("->", json_decode($userRole)->branch);
        $manager_path = explode("->", json_decode($ManagerRole)->branch);
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
        if (json_decode($userRole)->depth > json_decode($ManagerRole)->depth) {
            return true;
        }
        return false;
    }
}
