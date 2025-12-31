<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'password',
        'role_id',
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    protected $hidden = [
        'password'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function sent_messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function received_messages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function scopeHigherRankedThan($query, Role $managerRole)
    {
        $prefix = $managerRole->branch . '->%';

        return $query->whereHas('role', function ($q) use ($prefix) {
            $q->where('branch', 'like', $prefix);
        });
    }
}
