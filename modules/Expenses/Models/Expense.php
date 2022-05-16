<?php

declare(strict_types=1);

namespace Modules\Expenses\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\System\Database\CreatedByTrait;

class Expense extends Model
{
    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'name',
        'sum',
    ];
}
