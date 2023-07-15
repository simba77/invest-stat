<?php

declare(strict_types=1);

namespace Modules\Savings\Forms;

use Carbon\Carbon;
use Modules\Savings\Models\Saving;
use Modules\Savings\Models\SavingAccount;
use Modules\System\Forms\AbstractForm;
use Modules\System\Forms\Inputs\InputDate;
use Modules\System\Forms\Inputs\InputNumber;
use Modules\System\Forms\Inputs\Select;

/**
 * @property Saving $modelData
 */
class SavingForm extends AbstractForm
{
    public function setModelData(Saving $model): void
    {
        $this->modelData = $model;
    }

    public function form(): AbstractForm
    {
        $this->form = [
            'id' => $this->modelData ? $this->modelData->id : 0,

            'account' => (new Select())
                ->setNameAndId('account.value')
                ->setLabel('Account')
                ->setValidationRule('required')
                ->setItems(
                    fn() => SavingAccount::query()
                        ->forCurrentUser()
                        ->get()
                        ->map(fn($item) => [
                            'value' => $item->id,
                            'name'  => $item->name,
                        ])
                )
                ->setValue($this->getFieldValue('account.value'))
                ->get(),

            'sum' => (new InputNumber())
                ->setNameAndId('sum.value')
                ->setValidationRule('required')
                ->setLabel('Sum')
                ->setPlaceholder('Sum')
                ->setValue($this->getFieldValue('sum.value'))
                ->get(),

            'type' => (new Select())
                ->setNameAndId('type.value')
                ->setLabel('Type')
                ->setValidationRule('required')
                ->setItems(
                    fn() => [
                        [
                            'value' => 1,
                            'name'  => 'Deposit',
                        ],
                        [
                            'value' => 2,
                            'name'  => 'Percent',
                        ],
                    ]
                )
                ->setValue($this->getFieldValue('type.value', 1))
                ->get(),

            'date' => (new InputDate())
                ->setNameAndId('date.value')
                ->setValidationRule('required')
                ->setLabel('Date')
                ->setPlaceholder('Date')
                ->setValue($this->getDateField('date.value'))
                ->get(),
        ];

        return $this;
    }

    /** @inheritDoc */
    protected function getFieldsDefinition(): array
    {
        return [
            'saving_account_id' => 'account.value',
            'sum'               => 'sum.value',
            'type'              => 'type.value',
            'created_at'        => 'date.value',
        ];
    }

    private function getDateField(string $fieldName): string
    {
        if ($this->modelData) {
            return $this->modelData->$fieldName?->format('Y-m-d') ?? '';
        }

        return Carbon::now()->format('Y-m-d');
    }
}
