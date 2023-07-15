<?php

declare(strict_types=1);

namespace Modules\System\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

abstract class AbstractForm
{
    /** @var array Форма */
    public array $form = [];

    /** @var array Правила валидации формы */
    public array $validationRules = [];

    /** @var array Заполненные поля */
    public array $completedFields = [];

    /** @var int Идентификатор сущности, с которой ведется работа */
    public int $entityId = 0;

    /** @var Model|null Данные сущности */
    public ?Model $modelData = null;

    /**
     * Метод должен возвращать массив, содержащий форму
     *
     * @return self
     */
    abstract public function form(): AbstractForm;

    /**
     * Метод должен возвращать массив с определением полей ПолеВЗапросе => ПолеВБазеДанных
     *
     * @return array
     */
    abstract protected function getFieldsDefinition(): array;

    /**
     * Метод получает значения из полей
     */
    protected function getFieldValue(string $fieldName, mixed $defaultValue = ''): mixed
    {
        if (empty($this->modelData)) {
            return $defaultValue;
        }

        $fields = $this->getFieldsDefinition();
        $key = array_search($fieldName, $fields, true);
        if (! empty($key)) {
            return $this->modelData->$key;
        }
        return $defaultValue;
    }

    /**
     * Метод для получения формы
     */
    public function toArray(): array
    {
        return $this->form;
    }

    /**
     * Метод для json формы
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Метод возвращает правила валидации для формы
     */
    public function getValidationRules(): array
    {
        $this->collectValidationRules($this->form);
        return $this->validationRules;
    }

    /**
     * Метод собирает правила валидации из формы
     */
    public function collectValidationRules(array $form): void
    {
        foreach ($form as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (Arr::has($item, ['validationRule', 'id', 'name'])) {
                if (! empty($item['validationRule'])) {
                    $this->validationRules[$item['name']] = $item['validationRule'];
                }
            } else {
                $this->collectValidationRules($item);
            }
        }
    }

    /**
     * Метод собирает имена полей в которых хранятся значения в форме
     */
    public function getFieldsNames(array $form): void
    {
        foreach ($form as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (Arr::has($item, ['value', 'id', 'name'])) {
                $this->completedFields[$item['name']] = $item['value'];
            } else {
                $this->getFieldsNames($item);
            }
        }
    }

    /**
     * Метод собирает значения полей из запроса
     */
    public function getFieldsFromRequest(): array
    {
        $this->getFieldsNames($this->form);

        /** @var Request $request */
        $request = app(Request::class);

        $fieldsCompleted = [];

        $fields = $this->getFieldsDefinition();
        // Собираем поля из запроса
        foreach ($fields as $field => $path) {
            if (is_array($path)) {
                $values = [];
                foreach ($path as $sub_field => $sub_path) {
                    $values[$sub_field] = $request->input($sub_path);
                }
                $fieldsCompleted[$field] = $values;
                continue;
            }
            if (array_key_exists($path, $this->completedFields)) {
                $fieldsCompleted[$field] = $request->input($path);
            }
        }

        // Пробрасываем id сущности
        if (! empty($this->modelData)) {
            $fieldsCompleted['id'] = $this->entityId;
        }

        return $fieldsCompleted;
    }

    /**
     * Названия полей (ключ - название поля в request, значение - название для отображения)
     * 'active.value' => 'Активность'
     */
    public function validationAttributes(): array
    {
        return [];
    }


    /**
     * Выполняем валидацию данных формы
     */
    public function validate(): void
    {
        $request = app(Request::class);
        $validator = Validator::make(
            data:             $request->all(),
            rules:            $this->getValidationRules(),
            customAttributes: $this->validationAttributes()
        );
        $validator->validated();
    }
}
