<?php

declare(strict_types=1);

namespace Modules\Savings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Savings\Models\SavingAccount;

/**
 * @mixin SavingAccount
 */
class SavingAccountsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'   => $this->id,
            'date' => $this->created_at?->format('d.m.Y H:i'),
            'name' => $this->name,
        ];
    }
}
