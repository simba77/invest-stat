<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class InputNumber extends Input
{
    /**
     * @inheritDoc
     */
    public function get(): array
    {
        if ($this->accessDenied) {
            return [];
        }

        $arr = parent::get();
        $arr['type'] = 'number';

        return $arr;
    }
}
