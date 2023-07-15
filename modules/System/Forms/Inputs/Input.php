<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

/**
 * Class Input - абстрактный класс с обобщенными для всех полей методами
 */
abstract class Input
{
    public string $name = '';

    public mixed $value = '';

    public string $label = '';

    public string $placeholder = '';

    public mixed $id = '';

    public bool $readOnly = false;

    public bool $isHidden = false;

    public bool $accessDenied = false;

    public mixed $validationRule = '';

    public array $customFields = [];

    /**
     * Устанавливаем свойства id и name с одинаковым значением
     */
    public function setNameAndId(string $value): static
    {
        $this->name = $value;
        $this->id = $value;
        return $this;
    }

    /**
     * Имя поля
     */
    public function setName(string $value): static
    {
        $this->name = $value;
        return $this;
    }

    /**
     * Id поля
     */
    public function setId($value): static
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Label поля
     */
    public function setLabel(string $value): static
    {
        $this->label = $value;
        return $this;
    }

    /**
     * Placeholder
     */
    public function setPlaceholder(string $value): static
    {
        $this->placeholder = $value;
        return $this;
    }

    /**
     * Значение поля
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function setReadOnly(callable $readOnly): static
    {
        $this->readOnly = $readOnly();
        return $this;
    }

    /**
     * Пометка недоступности поля. Если флаг установлен, то поле класс должен возвращать пустой массив
     */
    public function setAccessDenied(callable $accessDenied): static
    {
        $this->accessDenied = $accessDenied();
        return $this;
    }

    /**
     * Добавление правила валидации
     */
    public function setValidationRule(array | string $value): static
    {
        $this->validationRule = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение кастомного поля.
     * Имена кастомных полей не заменяют собой имена основных, а при совпадении будут использоваться основные поля
     */
    public function setCustomField(string $fieldName, mixed $value): static
    {
        $this->customFields[$fieldName] = $value;
        return $this;
    }

    /**
     * Метод ставит флаг isHidden для скрытия элемента
     */
    public function hidden(): static
    {
        $this->isHidden = true;
        return $this;
    }

    /**
     * Метод должен реализовать сбор всех данных и подготовить поле для выдачи
     */
    public function get(): array
    {
        return array_merge(
            $this->customFields,
            [
                'id'             => $this->id,
                'name'           => $this->name,
                'label'          => $this->label,
                'placeholder'    => $this->placeholder,
                'value'          => $this->value,
                'isHidden'       => $this->isHidden,
                'readOnly'       => $this->readOnly,
                'validationRule' => $this->validationRule,
            ]
        );
    }
}
