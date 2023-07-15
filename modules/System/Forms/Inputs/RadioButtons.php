<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class RadioButtons extends Input
{
    public mixed $items = [];
    public string $color = 'success';

    /**
     * Метод для установки элементов селекта
     */
    public function setItems(callable $itemsLoader): static
    {
        $this->items = $itemsLoader();
        return $this;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        if ($this->accessDenied) {
            return [];
        }

        $arr = parent::get();
        $arr['type'] = 'radioButtons';
        $arr['items'] = $this->items;
        $arr['color'] = $this->color;

        return $arr;
    }
}
