<?php

declare(strict_types=1);

namespace Modules\Savings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Savings\Models\Saving;

/**
 * @mixin Saving
 */
class SavingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'date'     => $this->created_at?->format('d.m.Y H:i'),
            'sum'      => $this->sum,
            'type'     => $this->getType(),
            'account'  => new SavingAccountsResource($this->account),
            'currency' => '₽',
        ];
    }

    private function getType(): string
    {
        if ($this->type === 1) {
            return 'Deposit';
        }

        if ($this->type === 2) {
            return 'Percent';
        }

        return '';
    }
}
