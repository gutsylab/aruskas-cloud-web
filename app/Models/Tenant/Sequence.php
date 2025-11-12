<?php

namespace App\Models\Tenant;

use Exception;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use BelongsToTenant;

    //
}
