<?php

declare(strict_types=1);

namespace Modules\Savings\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Savings\Forms\SavingAccountForm;
use Modules\Savings\Models\SavingAccount;
use Modules\Savings\Resources\SavingAccountsResource;

class SavingAccountsController
{
    public function index(): AnonymousResourceCollection
    {
        $savings = SavingAccount::query()->forCurrentUser()->get();
        return SavingAccountsResource::collection($savings);
    }

    public function create(Request $request, SavingAccountForm $form, int $id = 0): array
    {
        $savingAccount = null;
        if ($id > 0) {
            $savingAccount = SavingAccount::query()->forCurrentUser()->findOrFail($id);
            $form->setModelData($savingAccount);
        }

        $form->form();
        if ($request->isMethod('POST')) {
            $form->validate();
            $fields = $form->getFieldsFromRequest();
            if ($id > 0) {
                $savingAccount->update($fields);
            } else {
                $fields['user_id'] = \Auth::user()->id;
                $savingAccount = SavingAccount::query()->create($fields);
            }
            return ['id' => $savingAccount->id];
        }

        return $form->toArray();
    }

    public function delete(int $id): array
    {
        $account = SavingAccount::query()->forCurrentUser()->findOrFail($id);
        $account->delete();
        return ['result' => 'ok'];
    }
}
