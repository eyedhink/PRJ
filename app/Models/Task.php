<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = 'tasks';
    protected $fillable = [
        'name',
        'description',
        'order',
        'user_id',
        'manager_id',
        'requirements',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'requirements' => 'array',
    ];

    public function tasked(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasker(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
