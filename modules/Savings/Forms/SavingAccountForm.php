<?php

declare(strict_types=1);

namespace Modules\Savings\Forms;

use Modules\Savings\Models\SavingAccount;
use Modules\System\Forms\AbstractForm;
use Modules\System\Forms\Inputs\InputText;

/**
 * @property SavingAccount $modelData
 */
class SavingAccountForm extends AbstractForm
{
    public function setModelData(SavingAccount $model): void
    {
        $this->modelData = $model;
    }

    public function form(): AbstractForm
    {
        $this->form = [
            'id' => $this->modelData ? $this->modelData->id : 0,

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
