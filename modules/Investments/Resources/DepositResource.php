<?php

declare(strict_types=1);

namespace Modules\Investments\Resources;

use Modules\Investments\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RealtyResource
 *
 * @mixin Deposit
 * @package Modules\Advertisement
 */
class DepositResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'date'     => $this->date->format('d.m.Y'),
            'sum'      => $this->sum,
            'account'  => $this->account?->name,
            'currency' => '₽',
        ];
    }
}
