<?php

declare(strict_types=1);

namespace Modules\Savings\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Savings\Models\Saving;
use Modules\Savings\Resources\SavingResource;

class SavingsController
{
    public function index(): AnonymousResourceCollection
    {
        $savings = Saving::query()->forCurrentUser()->get();
        return SavingResource::collection($savings);
    }

    public function create()
    {
    }

    public function delete(int $id)
    {
    }
}
