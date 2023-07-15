<?php

declare(strict_types=1);

namespace Modules\System\Forms\Elements;

class FormSubtitle extends AbstractElement
{
    protected string $type = 'subtitle';

    protected string $text = '';

    /**
     * Метод устанавливает текст заголовка
     *
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * @inerhitDoc
     */
    public function get(): array
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
        ];
    }
}
