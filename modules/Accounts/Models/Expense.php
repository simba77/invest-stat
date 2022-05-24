<?php

declare(strict_types=1);

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\System\Database\CreatedByTrait;

class Expense extends Model
{
    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'name',
        'sum',
        'category_id',
    ];

    public function category(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'category_id');
    }
}
