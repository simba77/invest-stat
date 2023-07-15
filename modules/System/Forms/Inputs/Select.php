<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class Select extends Input
{
    public mixed $items = [];
    public bool $defaultNothing = false;
    public bool $multiple = false;
    public bool $closeOnSelect = true;
    public string $mode = 'single';

    /**
     * Метод для установки элементов селекта
     */
    public function setItems(callable $items_loader): static
    {
        $this->items = $items_loader();
        return $this;
    }

    public function defaultNothing(): static
    {
        $this->defaultNothing = true;
        return $this;
    }

    public function closeOnSelect(bool $value): static
    {
        $this->closeOnSelect = $value;
        return $this;
    }

    public function multiple(): static
    {
        $this->mode = 'multiple';
        $this->multiple = true;
        return $this;
    }

    public function tags(): static
    {
        $this->mode = 'tags';
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
        $arr['type'] = 'select';
        $arr['items'] = $this->items;
        $arr['defaultNothing'] = $this->defaultNothing;
        $arr['multiple'] = $this->multiple;
        $arr['mode'] = $this->mode;
        $arr['closeOnSelect'] = $this->closeOnSelect;

        return $arr;
    }
}
