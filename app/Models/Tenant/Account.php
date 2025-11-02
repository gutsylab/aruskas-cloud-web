<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Account extends Model
{
    use BelongsToTenant;

    //
}
