<?php

namespace App\Observers;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class BaseModelObserver
{

    public function creating($model)
    {
        $userId = Auth::id();
        $model->created_by = $userId;
        $model->updated_by = $userId;
    }

    public function updating($model)
    {
        $model->updated_by = Auth::id();
    }

    public function deleting($model)
    {
        $model->deleted_by = Auth::id();
    }
}
