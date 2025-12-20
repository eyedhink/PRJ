<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Utils\Controllers\BaseController;
use App\Utils\Exceptions\TooLate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends BaseController
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
            validation_extensions: [
                'store' => [
                    'user_id' => fn(Request $request, array $validated) => $request->user('user')->id,
                ]
            ],
            selection_query: fn(Request $request): Builder => Report::with(['user'])->where('user_id', $request->user('user')->id),
//            access_checks: [
//                'is_too_late' => function (Request $request, array $validated, string $method) {
//                    if (str_starts_with($method, "edit")) {
//                        $report = Report::query()->findOrFail($request->query('kw'));
//                        var_dump($request->query('kw'));
//                        if (time() - Carbon::parse($report->created_at)->timestamp > 86400) {
//                            throw new TooLate();
//                        }
//                        return true;
//                    }
//                    return true;
//                }
//            ]
        );
    }
}
