<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
        );
    }
}
