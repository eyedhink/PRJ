<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Utils\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MessageController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Message::class,
            resource: MessageResource::class,
            loadRelations: ['sender', 'receiver'],
            validation: [
                'content' => ['required', 'string'],
                'receiver_id' => ['required', 'integer', 'exists:users,id'],
            ],
            validation_update: [
                'content' => ['required', 'string'],
            ],
            validation_extensions: [
                'store' => [
                    'sender_id' => fn(Request $request, array $validated) => $request->user('user')->id,
                ],
                'index' => [
                    'sender_id' => fn(Request $request, array $validated) => $request->user('user')->id,
                ]
            ],
            selection_query: fn(Request $request): Builder => Message::with(['sender', 'receiver'])
                ->where('sender_id', $request->user('user')->id)
                ->orWhere('receiver_id', $request->user('user')->id),
        );
    }
}
