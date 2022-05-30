<?php

declare(strict_types=1);

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Modules\System\Database\CreatedByTrait;

class Account extends Model
{
    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'currency',
        'start_sum_of_assets',
        'current_sum_of_assets',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'account_id', 'id');
    }

    public function scopeForCurrentUser(Builder $builder): Builder
    {
        return $builder->where('user_id', '=', Auth::user()?->id);
    }
}
