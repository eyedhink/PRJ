<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Manager extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'managers';

    protected $fillable = [
        'name',
        'password',
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    protected $hidden = [
        'password'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
