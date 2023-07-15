<?php

declare(strict_types=1);

namespace Modules\System\Forms\Elements;

abstract class AbstractElement
{
    protected string $type;

    /**
     * Метод должен возвращать массив с конечным набором полей
     *
     * @return array
     */
    abstract public function get(): array;
}
