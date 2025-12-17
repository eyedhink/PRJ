<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChatController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Chat::class,
            resource: ChatResource::class,
            loadRelations: ['user', 'manager', 'messages'],
            validation: [
                'manager_id' => ['required', 'integer', 'exists:managers,id'],
            ],
            validation_extensions: [
                'store' => [
                    'user_id' => fn(Request $request, array $validated) => $request->user('user')->id,
                ]
            ],
            selection_query: fn(Request $request): Builder => Chat::with(['user', 'manager', 'messages'])->where('user_id', $request->user('user')->id),
        );
    }
}
