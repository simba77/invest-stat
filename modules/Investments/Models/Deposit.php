<?php

declare(strict_types=1);

namespace Modules\Investments\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\System\Database\CreatedByTrait;

class Deposit extends Model
{
    use CreatedByTrait;

    protected $dates = ['date'];

    protected $fillable = [
        'user_id',
        'date',
        'sum',
    ];
}
