<?php

declare(strict_types=1);

namespace Modules\Savings\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Savings\Forms\SavingForm;
use Modules\Savings\Models\Saving;
use Modules\Savings\Resources\SavingResource;

class SavingsController
{
    public function index(): AnonymousResourceCollection
    {
        $savings = Saving::query()->forCurrentUser()->orderByDesc('created_at')->orderByDesc('id')->get();
        return SavingResource::collection($savings);
    }

    public function create(Request $request, SavingForm $form, int $id = 0): array
    {
        $saving = null;
        if ($id > 0) {
            $saving = Saving::query()->forCurrentUser()->findOrFail($id);
            $form->setModelData($saving);
        }

        $form->form();
        if ($request->isMethod('POST')) {
            $form->validate();
            $fields = $form->getFieldsFromRequest();

            if ($id > 0) {
                $saving->update($fields);
            } else {
                $fields['user_id'] = \Auth::user()->id;
                $saving = Saving::query()->create($fields);
            }
            return ['id' => $saving->id];
        }

        return $form->toArray();
    }

    public function delete(int $id): array
    {
        $saving = Saving::query()->forCurrentUser()->findOrFail($id);
        $saving->delete();
        return ['result' => 'ok'];
    }
}
