<?php

declare(strict_types=1);

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\System\Database\CreatedByTrait;

class Account extends Model
{
    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'currency',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }
}
