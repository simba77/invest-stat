<?php

declare(strict_types=1);

namespace Modules\Savings\Forms;

use Modules\System\Forms\AbstractForm;
use Modules\System\Forms\Inputs\InputText;

class SavingAccountForm extends AbstractForm
{
    public function form(): AbstractForm
    {
        $this->form = [
            'id' => 0,

            'name' => (new InputText())
                ->setNameAndId('name.value')
                ->setValidationRule('required')
                ->setLabel('Name')
                ->setPlaceholder('Name')
                ->setValue($this->getFieldValue('name.value'))
                ->get(),
        ];

        return $this;
    }

    /** @inheritDoc */
    protected function getFieldsDefinition(): array
    {
        return [
            'name' => 'name.value',
        ];
    }
}
