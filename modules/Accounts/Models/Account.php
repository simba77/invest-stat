<?php

declare(strict_types=1);

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Modules\Investments\Models\Deposit;
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
        'commission',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'account_id', 'id')->orderBy('ticker')->orderBy('created_at');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'account_id', 'id');
    }

    public function scopeForCurrentUser(Builder $builder): Builder
    {
        return $builder->where('user_id', '=', Auth::user()?->id);
    }

    public function scopeActiveAssets(Builder $builder): Builder
    {
        return $builder->with(
            [
                'assets' => function (HasMany $query) {
                    return $query->where('status', '!=', Asset::SOLD)->orWhereNull('status');
                },
            ]
        );
    }

    public function scopeSoldAssets(Builder $builder): Builder
    {
        return $builder->with(
            [
                'assets' => function (HasMany $query) {
                    return $query->where('status', '=', Asset::SOLD);
                },
            ]
        );
    }

    public function getProfitAttribute(): float
    {
        return $this->current_sum_of_assets + $this->balance - $this->start_sum_of_assets;
    }

    public function getProfitPercentAttribute(): float
    {
        if ($this->start_sum_of_assets > 0) {
            return round(($this->current_sum_of_assets + $this->balance - $this->start_sum_of_assets) / $this->start_sum_of_assets * 100, 2);
        }
        return 0;
    }
}
