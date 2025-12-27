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

    // 1: Financial Manager->A
    // 2: Financial Manager->B
    // 3: Financial Manager->B->C
    // 4: Financial Manager->A->F
    // 5: Marketer->D->E
    // Only (2) is Ranked Higher than (3)
    public function scopeHigherRankedThan($query, Role $managerRole)
    {
        $expectedDepth = $managerRole->depth + 1;
        $prefix = $managerRole->branch . '->%';

        return $query->whereHas('role', function ($q) use ($expectedDepth, $prefix) {
            $q->whereRaw('roles.branch LIKE ?', [$prefix])
                ->where('roles.depth', $expectedDepth);
        });
    }
}
