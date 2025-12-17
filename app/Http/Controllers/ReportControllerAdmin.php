<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Utils\Controllers\BaseController;

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
            ]
        );
    }
}
