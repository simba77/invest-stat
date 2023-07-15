<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class InputDate extends Input
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
        $arr['type'] = 'date';
        $arr['format'] = 'dd/mm/yyyy';

        return $arr;
    }
}
