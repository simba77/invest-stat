<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class Autocomplete extends Input
{
    public string $sourceUrl = '';
    public mixed $currentItem = [];

    /**
     * URL к которому будет возвращать список элементов для автокомплита
     */
    public function setSourceUrl(string $value): static
    {
        $this->sourceUrl = $value;
        return $this;
    }

    public function setCurrentItem(callable $currentItem): static
    {
        $this->currentItem = $currentItem();
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
        return array_merge(
            $arr,
            [
                'type'           => 'text',
                'sourceUrl'      => $this->sourceUrl,
                'currentItem'    => $this->currentItem,
                'validationRule' => $this->validationRule,
            ]
        );
    }
}
