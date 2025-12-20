<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Utils\Controllers\BaseController;

class RoleController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Role::class,
            resource: RoleResource::class,
            loadRelations: ['master', 'slaves'],
            validation: [
                'abilities' => ['required', 'array'],
                'abilities.*' => ['required_with:abilities', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:255', 'unique:roles,title'],
                'master_id' => ['nullable', 'integer', 'exists:roles,id'],
            ]
        );
    }
}
