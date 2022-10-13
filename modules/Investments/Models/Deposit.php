<?php

declare(strict_types=1);

namespace Modules\Investments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Accounts\Models\Account;
use Modules\System\Database\CreatedByTrait;

class Deposit extends Model
{
    use CreatedByTrait;

    protected $dates = ['date'];

    protected $fillable = [
        'user_id',
        'date',
        'sum',
        'account_id',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
