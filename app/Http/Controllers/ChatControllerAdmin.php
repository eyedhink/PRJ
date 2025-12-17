<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Utils\Controllers\BaseController;
use Illuminate\Http\Request;

class ChatControllerAdmin extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Chat::class,
            resource: ChatResource::class,
            loadRelations: ['user', 'manager', 'messages'],
            validation: [
                'user_id' => ['required', 'integer', 'exists:users,id'],
            ],
            validation_extensions: [
                'store' => [
                    'manager_id' => fn(Request $request, array $validated) => $request->user('manager')->id,
                ]
            ],
        );
    }
}
