<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'title',
        'abilities',
        'master_id',
        'branch',
        'depth',
    ];

    protected $casts = [
        'abilities' => 'array',
    ];

    public function master(): BelongsTo
    {
        return $this->BelongsTo(Role::class, 'master_id');
    }

    public function slaves(): HasMany
    {
        return $this->hasMany(Role::class, 'master_id');
    }
}
