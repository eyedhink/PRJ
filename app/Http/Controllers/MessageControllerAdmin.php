<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MessageControllerAdmin extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Message::class,
            resource: MessageResource::class,
            loadRelations: ['manager', 'chat', 'user'],
            validation: [
                'content' => ['required', 'string'],
                'chat_id' => ['required', 'integer', 'exists:chats,id'],
            ],
            validation_index: [
                'chat_id' => ['required', 'integer', 'exists:chats,id'],
            ],
            validation_update: [
                'content' => ['required', 'string'],
            ],
            validation_extensions: [
                'store' => [
                    'manager_id' => fn(Request $request, array $validated) => $request->user('manager')->id,
                ],
                'index' => [
                    'manager_id' => fn(Request $request, array $validated) => $request->user('manager')->id,
                ]
            ],
            selection_query: fn(Request $request): Builder => Message::with(['manager', 'chat'])->where('manager_id', $request->user('manager')->id),
            selection_query_blacklist: [
                'index',
                'show'
            ],
            selection_query_replace: [
                'index' => fn(Request $request, array $validated): Builder => Message::with(['manager', 'chat'])
                    ->where('chat_id', $validated['chat_id']),
            ]
        );
    }
}
