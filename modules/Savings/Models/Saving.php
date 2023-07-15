<?php

declare(strict_types=1);

namespace Modules\Savings\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Modules\System\Database\CreatedByTrait;

class Saving extends Model
{
    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'saving_account_id',
        'sum',
        'type',
        'created_at',
    ];

    public function scopeForCurrentUser(Builder $builder): Builder
    {
        return $builder->where('user_id', '=', Auth::user()?->id);
    }

    public function account(): HasOne
    {
        return $this->hasOne(SavingAccount::class, 'id', 'saving_account_id');
    }
}
